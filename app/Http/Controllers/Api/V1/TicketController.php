<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TicketMessageType;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Events\UserNotificationReceived;
use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketGroup;
use App\Models\TicketMessage;
use App\Models\TicketPriority;
use App\Models\User;
use App\Notifications\TicketReopenedNotification;
use App\Services\ApprovalService;
use App\Services\AutomationService;
use App\Services\OrganizationAuditService;
use App\Services\SlaService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orgId = $request->attributes->get('apiOrganizationId');
        $perPage = min((int) $request->input('per_page', 20), 100);

        $query = Ticket::where('organization_id', $orgId)->active();

        if ($request->filled('status')) {
            $status = TicketStatus::tryFrom($request->input('status'));
            if ($status) {
                $query->where('status', $status);
            }
        }
        if ($request->filled('priority_id')) {
            $query->where('ticket_priority_id', (int) $request->input('priority_id'));
        }
        if ($request->filled('category_id')) {
            $query->where('ticket_category_id', (int) $request->input('category_id'));
        }
        if ($request->filled('group_id')) {
            $query->where('ticket_group_id', (int) $request->input('group_id'));
        }
        if ($request->filled('assigned_to')) {
            $query->whereHas('assignees', fn ($q) => $q->where('users.id', (int) $request->input('assigned_to')));
        }
        if ($request->filled('created_after')) {
            $query->where('created_at', '>=', $request->input('created_after'));
        }
        if ($request->filled('created_before')) {
            $query->where('created_at', '<=', $request->input('created_before'));
        }
        if ($request->filled('search')) {
            $query->where('subject', 'ilike', '%'.$request->input('search').'%');
        }

        $tickets = $query->with(['category', 'priority', 'group', 'creator', 'assignees'])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return TicketResource::collection($tickets);
    }

    public function show(Request $request, string $publicId): TicketResource
    {
        $orgId = $request->attributes->get('apiOrganizationId');

        $ticket = Ticket::where('organization_id', $orgId)
            ->where('public_id', $publicId)
            ->with(['category', 'priority', 'group', 'creator', 'assignees'])
            ->firstOrFail();

        return new TicketResource($ticket);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $orgId = $request->attributes->get('apiOrganizationId');
        $user = $request->user();

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'ticket_category_id' => ['nullable', 'integer'],
            'ticket_priority_id' => ['nullable', 'integer'],
            'ticket_group_id' => ['nullable', 'integer'],
            'assigned_to_ids' => ['nullable', 'array'],
            'assigned_to_ids.*' => ['integer'],
            'due_date' => ['nullable', 'date'],
            'custom_fields' => ['nullable', 'array'],
        ]);

        // Validate category belongs to org (fallback to first active if not provided)
        $categoryId = $validated['ticket_category_id'] ?? null;
        if ($categoryId) {
            $catOk = TicketCategory::where('id', $categoryId)
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->exists();
            if (! $catOk) {
                return response()->json(['error' => 'Catégorie invalide.'], 422);
            }
        } else {
            $categoryId = TicketCategory::where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('id')
                ->value('id');
            if (! $categoryId) {
                return response()->json(['error' => 'Aucune catégorie active trouvée dans l\'organisation.'], 422);
            }
        }

        // Validate priority belongs to org (fallback to first active if not provided)
        $priorityId = $validated['ticket_priority_id'] ?? null;
        if ($priorityId) {
            $prioOk = TicketPriority::where('id', $priorityId)
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->exists();
            if (! $prioOk) {
                return response()->json(['error' => 'Priorité invalide.'], 422);
            }
        } else {
            $priorityId = TicketPriority::where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('id')
                ->value('id');
        }

        // Validate group belongs to org, or default to token-named group
        $groupId = $validated['ticket_group_id'] ?? null;
        if ($groupId) {
            $groupOk = TicketGroup::where('id', $groupId)
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->exists();
            if (! $groupOk) {
                return response()->json(['error' => 'Groupe invalide.'], 422);
            }
        } else {
            // Auto-assign to a group matching the API token name
            $tokenName = $user->currentAccessToken()?->name;
            if ($tokenName) {
                $group = TicketGroup::firstOrCreate(
                    ['organization_id' => $orgId, 'slug' => \Illuminate\Support\Str::slug($tokenName)],
                    [
                        'name' => $tokenName,
                        'is_active' => true,
                        'sort_order' => 0,
                    ]
                );
                $groupId = (int) $group->id;
            }
        }

        $assigneeIds = array_map('intval', array_filter($validated['assigned_to_ids'] ?? []));
        if (! empty($assigneeIds)) {
            $validAssignees = User::query()
                ->whereIn('id', $assigneeIds)
                ->assignableInOrganization($orgId)
                ->count();

            if ($validAssignees !== count($assigneeIds)) {
                return response()->json(['error' => 'Un ou plusieurs assignés ne font pas partie de l\'équipe interne.'], 422);
            }
        }
        $firstAssignee = $assigneeIds[0] ?? null;

        $ticket = Ticket::create([
            'organization_id' => $orgId,
            'created_by' => $user->id,
            'ticket_category_id' => $categoryId,
            'ticket_priority_id' => $priorityId,
            'ticket_group_id' => $groupId,
            'assigned_to' => $firstAssignee,
            'assigned_by' => $firstAssignee ? $user->id : null,
            'assigned_at' => $firstAssignee ? now() : null,
            'status' => TicketStatus::Open,
            'source' => TicketSource::Api,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'custom_fields' => $validated['custom_fields'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        SlaService::applyPolicy($ticket);
        ApprovalService::applyPolicy($ticket);

        if (! empty($assigneeIds)) {
            $syncData = [];
            foreach ($assigneeIds as $aid) {
                $syncData[$aid] = ['assigned_by' => $user->id];
            }
            $ticket->assignees()->sync($syncData);
        }

        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);

        AutomationService::evaluate($ticket, 'ticket_created');

        $ticket->load(['category', 'priority', 'group', 'creator', 'assignees']);

        WebhookService::dispatch($orgId, 'ticket.created', [
            'ticket' => (new TicketResource($ticket))->resolve(),
        ]);

        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, string $publicId): TicketResource|\Illuminate\Http\JsonResponse
    {
        return $this->handleUpdate($request, $publicId);
    }

    private function handleUpdate(Request $request, string $publicId): TicketResource|\Illuminate\Http\JsonResponse
    {
        return $this->performUpdate($request, $publicId);
    }

    private function performUpdate(Request $request, string $publicId): TicketResource|\Illuminate\Http\JsonResponse
    {
        $orgId = $request->attributes->get('apiOrganizationId');
        $user = $request->user();

        $ticket = Ticket::where('organization_id', $orgId)
            ->where('public_id', $publicId)
            ->firstOrFail();

        $validated = $request->validate([
            'status' => ['nullable', 'string'],
            'subject' => ['nullable', 'string', 'max:255'],
            'ticket_priority_id' => ['nullable', 'integer'],
            'ticket_group_id' => ['nullable', 'integer'],
            'assigned_to_ids' => ['nullable', 'array'],
            'assigned_to_ids.*' => ['integer'],
            'due_date' => ['nullable', 'date'],
        ]);

        $oldStatus = $ticket->status;
        $oldAssigneeIds = $ticket->assignees()->pluck('users.id')->all();

        $statusResponse = $this->applyStatusUpdate($ticket, $validated, $oldStatus, $user, (int) $orgId);
        if ($statusResponse) {
            return $statusResponse;
        }
        $this->applySimpleTicketUpdates($ticket, $validated);
        $assigneeResponse = $this->applyAssigneeUpdates($ticket, $validated, $oldAssigneeIds, $user, (int) $orgId);
        if ($assigneeResponse) {
            return $assigneeResponse;
        }

        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);

        $ticket->load(['category', 'priority', 'group', 'creator', 'assignees']);

        return new TicketResource($ticket);
    }

    private function applyStatusUpdate(Ticket $ticket, array $validated, TicketStatus $oldStatus, mixed $user, int $orgId): ?\Illuminate\Http\JsonResponse
    {
        if (! isset($validated['status'])) {
            return null;
        }

        $newStatus = TicketStatus::tryFrom($validated['status']);
        if (! $newStatus || $newStatus === $oldStatus) {
            return null;
        }

        $isClosed = in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true);
        $ticket->update([
            'status' => $newStatus,
            'closed_by' => $isClosed ? $user->id : null,
            'closed_at' => $isClosed ? now() : null,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'type' => TicketMessageType::System,
            'body' => "Statut modifié via API : {$oldStatus->value} → {$newStatus->value}",
            'meta' => ['action' => 'status_changed', 'old' => $oldStatus->value, 'new' => $newStatus->value],
        ]);
        OrganizationAuditService::log('ticket.status_changed', 'Ticket', $ticket->id, [
            'old_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
        ]);

        if ($newStatus === TicketStatus::Pending) {
            SlaService::pause($ticket);
        } elseif ($oldStatus === TicketStatus::Pending && $newStatus !== TicketStatus::Pending) {
            SlaService::resume($ticket);
        }
        if (in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            SlaService::recordResolution($ticket);
        }
        if (in_array($oldStatus, [TicketStatus::Resolved, TicketStatus::Closed], true)
            && ! in_array($newStatus, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            SlaService::onReopened($ticket);
            $this->notifyReopenedTicket($ticket, (int) $user->id, (string) $user->name);
        }

        AutomationService::evaluate($ticket, 'status_changed');
        WebhookService::dispatch($orgId, 'ticket.status_changed', [
            'ticket_id' => $ticket->public_id,
            'old_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
            'changed_by' => $user->id,
        ]);

        return null;
    }

    private function notifyReopenedTicket(Ticket $ticket, int $actorId, string $actorName): void
    {
        $notifyUserIds = collect([(int) $ticket->created_by])
            ->merge($ticket->assignees()->pluck('users.id'))
            ->merge($ticket->participants()->pluck('users.id'))
            ->filter()
            ->unique()
            ->diff([$actorId])
            ->values();
        if ($notifyUserIds->isEmpty()) {
            return;
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $recipients */
        $recipients = User::query()->whereIn('id', $notifyUserIds)->get();
        foreach ($recipients as $recipient) {
            if ($ticket->hasDiscussionAccess((int) $recipient->id)) {
                $recipient->notify(new TicketReopenedNotification($ticket, $actorId, $actorName));
                event(new UserNotificationReceived((int) $recipient->id, 'ticket_reopened'));
            }
        }
    }

    private function applySimpleTicketUpdates(Ticket $ticket, array $validated): void
    {
        $updates = [];
        if (isset($validated['subject'])) {
            $updates['subject'] = $validated['subject'];
        }
        if (array_key_exists('ticket_priority_id', $validated)) {
            $updates['ticket_priority_id'] = $validated['ticket_priority_id'];
        }
        if (array_key_exists('ticket_group_id', $validated)) {
            $updates['ticket_group_id'] = $validated['ticket_group_id'];
        }
        if (array_key_exists('due_date', $validated)) {
            $updates['due_date'] = $validated['due_date'];
        }
        if (! empty($updates)) {
            $ticket->update($updates);
        }
    }

    private function applyAssigneeUpdates(Ticket $ticket, array $validated, array $oldAssigneeIds, mixed $user, int $orgId): ?\Illuminate\Http\JsonResponse
    {
        if (! isset($validated['assigned_to_ids'])) {
            return null;
        }

        $newAssigneeIds = array_values(array_filter(array_map('intval', $validated['assigned_to_ids'])));
        if (! empty($newAssigneeIds)) {
            $validAssignees = User::query()
                ->whereIn('id', $newAssigneeIds)
                ->assignableInOrganization($orgId)
                ->count();

            if ($validAssignees !== count($newAssigneeIds)) {
                return response()->json(['error' => 'Un ou plusieurs assignés ne font pas partie de l\'équipe interne.'], 422);
            }
        }

        $syncData = [];
        foreach ($newAssigneeIds as $aid) {
            $syncData[$aid] = ['assigned_by' => $user->id];
        }
        $ticket->assignees()->sync($syncData);
        $ticket->update([
            'assigned_to' => $newAssigneeIds[0] ?? null,
            'assigned_by' => ! empty($newAssigneeIds) ? $user->id : null,
            'assigned_at' => ! empty($newAssigneeIds) ? now() : null,
        ]);

        if ($newAssigneeIds !== $oldAssigneeIds) {
            WebhookService::dispatch($orgId, 'ticket.assigned', [
                'ticket_id' => $ticket->public_id,
                'assignees' => collect($newAssigneeIds)->map(fn ($id) => [
                    'id' => $id,
                    'name' => User::find($id)?->name,
                ])->all(),
                'changed_by' => $user->id,
            ]);
        }

        return null;
    }
}
