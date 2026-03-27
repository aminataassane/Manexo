<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\Permission;
use App\Enums\TicketMessageType;
use App\Events\TicketMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TicketCommentResource;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Services\SlaService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketCommentController extends Controller
{
    public function index(Request $request, string $publicId): AnonymousResourceCollection
    {
        $orgId = $request->attributes->get('apiOrganizationId');

        $ticket = Ticket::where('organization_id', $orgId)
            ->where('public_id', $publicId)
            ->firstOrFail();

        $query = $ticket->messages()->with('user:id,name,email');

        // Hide internal notes unless user has permission
        $user = $request->user();
        if (! $user->hasPermission(Permission::DiscussionsViewInternalNotes)) {
            $query->where('type', '!=', TicketMessageType::InternalNote);
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $comments = $query->orderBy('created_at')->paginate($perPage);

        return TicketCommentResource::collection($comments);
    }

    public function store(Request $request, string $publicId): \Illuminate\Http\JsonResponse
    {
        $orgId = $request->attributes->get('apiOrganizationId');
        $user = $request->user();

        $ticket = Ticket::where('organization_id', $orgId)
            ->where('public_id', $publicId)
            ->firstOrFail();

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
            'internal' => ['nullable', 'boolean'],
        ]);

        $isInternal = (bool) ($validated['internal'] ?? false);
        $type = TicketMessageType::Message;

        if ($isInternal && $user->hasPermission(Permission::DiscussionsWriteInternalNotes)) {
            $type = TicketMessageType::InternalNote;
        }

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'type' => $type,
            'body' => trim($validated['body']),
        ]);

        event(new TicketMessageSent($message));

        // SLA: record first response if not from ticket creator
        if ($type === TicketMessageType::Message && (int) $user->id !== (int) $ticket->created_by) {
            SlaService::recordFirstResponse($ticket);
        }

        if ($type === TicketMessageType::Message) {
            WebhookService::dispatch($orgId, 'ticket.comment_created', [
                'ticket_id' => $ticket->public_id,
                'comment' => (new TicketCommentResource($message->load('user')))->resolve(),
            ]);
        }

        return (new TicketCommentResource($message->load('user')))
            ->response()
            ->setStatusCode(201);
    }
}
