<?php

namespace App\Livewire\Tickets;

use App\Enums\FormStatus;
use App\Enums\Permission;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use App\Events\UserNotificationReceived;
use App\Helpers\CacheHelper;
use App\Models\Form;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketChecklistItem;
use App\Models\TicketGroup;
use App\Models\TicketPriority;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use App\Services\ApprovalService;
use App\Services\AutomationService;
use App\Services\SlaService;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.manexo-app')]
#[Title('Créer un ticket')]
class Create extends Component
{
    use WithFileUploads;

    public bool $ready = true;

    public ?int $ticket_category_id = null;

    public ?int $ticket_priority_id = null;

    public ?int $ticket_group_id = null;

    /** @var array<int, int> */
    public array $assigned_to_ids = [];

    public ?string $start_date = null;

    public ?string $due_date = null;

    public string $subject = '';

    public string $description = '';

    /** @var array<string, mixed> */
    public array $custom = [];

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $files = [];

    /** @var array<int, string> */
    public array $links = [];

    public string $linkUrl = '';

    /** Checklist initiale : liste de ['title' => string, 'assigned_to' => ?int, 'due_date' => ?string] */
    public array $checklistItems = [];

    /** KB deflection suggestions */
    public array $kbSuggestions = [];

    /** KB browser */
    public bool $showKbBrowser = false;

    public string $kbSearchTerm = '';

    public array $kbSearchResults = [];

    public bool $canAssignAtCreate = false;

    public function updatedSubject(string $value): void
    {
        $this->kbSuggestions = [];

        if (mb_strlen(trim($value)) < 3) {
            return;
        }

        $orgId = (int) session('current_organization_id');
        if (! $orgId) {
            return;
        }

        $needle = trim($value);
        $cacheKey = 'create_ticket:kb_suggestions:'.$orgId.':'.md5(mb_strtolower($needle));
        $this->kbSuggestions = Cache::remember($cacheKey, 60, function () use ($orgId, $needle) {
            $term = '%'.$needle.'%';

            return \App\Models\KbArticle::withoutOrganizationScope()
                ->where('organization_id', $orgId)
                ->published()
                ->where(function ($q) use ($term) {
                    $q->whereRaw('LOWER(title) LIKE LOWER(?)', [$term])
                        ->orWhereRaw('LOWER(content) LIKE LOWER(?)', [$term])
                        ->orWhereRaw("EXISTS (SELECT 1 FROM jsonb_array_elements_text(COALESCE(keywords, '[]'::jsonb)) kw WHERE LOWER(kw) LIKE LOWER(?))", [$term]);
                })
                ->orderByDesc('view_count')
                ->limit(5)
                ->get(['id', 'title', 'content', 'view_count'])
                ->map(fn (\App\Models\KbArticle $a) => [
                    'id' => $a->id,
                    'title' => $a->title,
                    'excerpt' => $a->excerpt(200),
                    'view_count' => $a->view_count,
                ])
                ->toArray();
        });
    }

    public function openKbBrowser(): void
    {
        $this->showKbBrowser = true;
        $this->kbSearchTerm = '';
        $this->kbSearchResults = [];
    }

    public function updatedKbSearchTerm(): void
    {
        $this->searchKbArticles();
    }

    public function searchKbArticles(): void
    {
        $this->kbSearchResults = [];

        if (mb_strlen(trim($this->kbSearchTerm)) < 2) {
            return;
        }

        $orgId = (int) session('current_organization_id');
        if (! $orgId) {
            return;
        }

        $needle = trim($this->kbSearchTerm);
        $cacheKey = 'create_ticket:kb_search:'.$orgId.':'.md5(mb_strtolower($needle));
        $this->kbSearchResults = Cache::remember($cacheKey, 60, function () use ($orgId, $needle) {
            $term = '%'.$needle.'%';

            return \App\Models\KbArticle::withoutOrganizationScope()
                ->where('organization_id', $orgId)
                ->published()
                ->where(function ($q) use ($term) {
                    $q->whereRaw('LOWER(title) LIKE LOWER(?)', [$term])
                        ->orWhereRaw('LOWER(content) LIKE LOWER(?)', [$term])
                        ->orWhereRaw("EXISTS (SELECT 1 FROM jsonb_array_elements_text(COALESCE(keywords, '[]'::jsonb)) kw WHERE LOWER(kw) LIKE LOWER(?))", [$term]);
                })
                ->orderByDesc('view_count')
                ->limit(10)
                ->get(['id', 'title', 'content', 'view_count'])
                ->map(fn (\App\Models\KbArticle $a) => [
                    'id' => $a->id,
                    'title' => $a->title,
                    'excerpt' => $a->excerpt(300),
                    'safeHtml' => $a->safeHtml(),
                    'view_count' => $a->view_count,
                ])
                ->toArray();
        });
    }

    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_if(! $user, 403);
        abort_if(! $user->hasPermission(Permission::TicketsCreate), 403);

        $orgId = (int) session('current_organization_id');
        abort_if(! $orgId, 403);

        $this->canAssignAtCreate = $user->hasPermission(Permission::TicketsAssign);

        // Set defaults once at mount (avoid mutating state during render).
        $categories = Cache::remember(CacheHelper::categoriesKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
            return TicketCategory::query()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'default_ticket_group_id']);
        });
        if ($this->ticket_category_id === null && $categories->isNotEmpty()) {
            $this->ticket_category_id = (int) $categories->first()->id;
            $defaultGroupId = $categories->first()->default_ticket_group_id ?? null;
            if ($defaultGroupId) {
                $this->ticket_group_id = (int) $defaultGroupId;
            }
        }

        $priorities = Cache::remember(CacheHelper::prioritiesKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
            return TicketPriority::query()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('level')
                ->get(['id']);
        });
        if ($this->ticket_priority_id === null && $priorities->isNotEmpty()) {
            $this->ticket_priority_id = (int) $priorities->first()->id;
        }
    }

    public function updatedTicketCategoryId($value): void
    {
        $this->custom = [];

        if (! $value) {
            return;
        }

        $orgId = (int) session('current_organization_id');
        if (! $orgId) {
            return;
        }

        $categories = Cache::remember(CacheHelper::categoriesKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
            return TicketCategory::query()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'default_ticket_group_id']);
        });
        $category = $categories->firstWhere('id', (int) $value);

        if ($category && $category->default_ticket_group_id) {
            $this->ticket_group_id = $category->default_ticket_group_id;
        }
    }

    public function submit(): void
    {
        $this->handleSubmit();
    }

    private function handleSubmit(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $orgId = (int) session('current_organization_id');

        if (! ($user instanceof User) || ! $orgId) {
            $this->redirectRoute('organizations.select');

            return;
        }

        $form = $this->resolveForm($orgId, (int) ($this->ticket_category_id ?? 0), (int) $user->id);
        $fields = $form?->fields?->where('type', '!=', 'section') ?? collect();

        $dynamicRules = [];
        foreach ($fields as $f) {
            $path = "custom.{$f->key}";
            $rules = [$f->required ? 'required' : 'nullable'];

            $type = (string) $f->type;
            $options = $f->options ?? [];
            if ($type === 'email') {
                $rules[] = 'email';
                $rules[] = 'max:255';
            } elseif ($type === 'number') {
                $rules[] = 'numeric';
            } elseif (in_array($type, ['date', 'datetime'], true)) {
                $rules[] = 'date';
            } elseif ($type === 'checkbox') {
                if (is_array($options) && count($options) > 0) {
                    $rules[] = 'array';
                    $rules[] = 'max:'.count($options);
                    if ($f->required) {
                        $rules[] = 'min:1';
                    }
                    $dynamicRules["custom.{$f->key}.*"] = ['string', Rule::in($options)];
                } else {
                    $rules[] = 'boolean';
                }
            } elseif ($type === 'textarea') {
                $rules[] = 'string';
                $rules[] = 'max:5000';
            } elseif (in_array($type, ['select', 'radio'], true)) {
                $rules[] = 'string';
                $rules[] = 'max:120';
                if (is_array($options) && count($options)) {
                    $rules[] = Rule::in($options);
                }
            } elseif ($type === 'file') {
                $rules = ['nullable', 'file', 'max:10240'];
            } else {
                // text
                $rules[] = 'string';
                $rules[] = 'max:255';
            }

            $dynamicRules[$path] = $rules;
        }

        $baseRules = [
            'ticket_category_id' => ['required', 'integer', 'exists:ticket_categories,id'],
            'ticket_priority_id' => ['required', 'integer', 'exists:ticket_priorities,id'],
            'ticket_group_id' => ['nullable', 'integer', 'exists:ticket_groups,id'],
            'assigned_to_ids' => ['nullable', 'array'],
            'assigned_to_ids.*' => ['integer', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'files' => ['array', 'max:5'],
            'files.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt,zip'],
            'links' => ['array', 'max:5'],
            'links.*' => ['url', 'max:2000'],
            'checklistItems' => ['array', 'max:50'],
            'checklistItems.*.title' => ['nullable', 'string', 'max:500'],
            'checklistItems.*.assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'checklistItems.*.due_date' => ['nullable', 'date'],
        ];
        $validated = $this->validate(array_merge($baseRules, $dynamicRules));

        // Enforce scoping to current organization
        $categoryOk = TicketCategory::query()
            ->where('id', $validated['ticket_category_id'])
            ->where('organization_id', $orgId)
            ->exists();

        $priorityOk = TicketPriority::query()
            ->where('id', $validated['ticket_priority_id'])
            ->where('organization_id', $orgId)
            ->exists();

        $groupOk = true;
        if (! empty($validated['ticket_group_id'])) {
            $groupOk = TicketGroup::query()
                ->where('id', (int) $validated['ticket_group_id'])
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->exists();
        }

        if (! $categoryOk || ! $priorityOk || ! $groupOk) {
            $this->addError('ticket_category_id', "Sélection invalide pour l'entreprise.");

            return;
        }

        // Ensure all assignees are internal staff of current org
        $assigneeIds = array_map('intval', array_filter($validated['assigned_to_ids'] ?? []));
        if (! empty($assigneeIds)) {
            $validCount = User::query()
                ->whereIn('id', $assigneeIds)
                ->assignableInOrganization($orgId)
                ->count();

            if ($validCount !== count($assigneeIds)) {
                $this->addError('assigned_to_ids', 'Un ou plusieurs assignés ne font pas partie de l\'équipe interne.');

                return;
            }
        }

        // Ensure checklist assignees are also internal staff of current org
        $checklistAssigneeIds = collect($validated['checklistItems'] ?? [])
            ->pluck('assigned_to')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
        if (! empty($checklistAssigneeIds)) {
            $validChecklistAssignees = User::query()
                ->whereIn('id', $checklistAssigneeIds)
                ->assignableInOrganization($orgId)
                ->count();

            if ($validChecklistAssignees !== count($checklistAssigneeIds)) {
                $this->addError('checklistItems', 'Un ou plusieurs assignés de checklist ne font pas partie de l\'équipe interne.');

                return;
            }
        }

        $customFields = [];
        $options = [];
        foreach ($fields as $f) {
            $options[$f->key] = is_array($f->options) ? $f->options : [];
        }
        foreach ($fields as $f) {
            $key = (string) $f->key;
            $val = $this->custom[$key] ?? null;
            if ($f->type === 'checkbox') {
                $opts = $options[$key] ?? [];
                if (count($opts) > 0) {
                    $val = is_array($val) ? array_values(array_filter($val)) : [];
                } else {
                    $val = (bool) $val;
                }
            }
            if (is_string($val)) {
                $val = trim($val);
            }

            // Keep false/0, ignore empty strings/null; for array keep empty array
            if ($val === null || $val === '' || (is_array($val) && count($val) === 0)) {
                continue;
            }
            $customFields[$key] = $val;
        }

        $firstAssignee = $assigneeIds[0] ?? null;
        $ticket = Ticket::create([
            'organization_id' => $orgId,
            'created_by' => $user->id,
            'ticket_category_id' => $validated['ticket_category_id'],
            'ticket_priority_id' => $validated['ticket_priority_id'],
            'ticket_group_id' => ! empty($validated['ticket_group_id']) ? (int) $validated['ticket_group_id'] : null,
            'assigned_to' => $firstAssignee,
            'assigned_by' => $firstAssignee ? $user->id : null,
            'assigned_at' => $firstAssignee ? now() : null,
            'status' => TicketStatus::Open,
            'source' => TicketSource::Platform,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'custom_fields' => $customFields ?: null,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        SlaService::applyPolicy($ticket);
        ApprovalService::applyPolicy($ticket);

        // Sync assignees to the pivot table
        if (! empty($assigneeIds)) {
            $syncData = [];
            foreach ($assigneeIds as $aid) {
                $syncData[$aid] = ['assigned_by' => $user->id];
            }
            $ticket->assignees()->sync($syncData);
        }

        $attachments = [
            'files' => [],
            'links' => [],
        ];

        foreach (($validated['files'] ?? []) as $file) {
            $original = (string) ($file->getClientOriginalName() ?: 'file');
            $ext = (string) ($file->getClientOriginalExtension() ?: '');
            $safeBase = Str::slug(pathinfo($original, PATHINFO_FILENAME)) ?: 'file';
            $filename = $safeBase.'-'.Str::lower(Str::random(10)).($ext ? '.'.$ext : '');

            $path = $file->storeAs("ticket-attachments/org-{$orgId}/ticket-{$ticket->id}", $filename, 'local');

            $attachments['files'][] = [
                'disk' => 'local',
                'path' => $path,
                'name' => $original,
                'size' => method_exists($file, 'getSize') ? (int) $file->getSize() : null,
                'mime' => method_exists($file, 'getMimeType') ? (string) $file->getMimeType() : null,
                'url' => route('tickets.attachment', ['ticket' => $ticket, 'filename' => $filename]),
            ];
        }

        foreach (($validated['links'] ?? []) as $url) {
            $attachments['links'][] = [
                'url' => (string) $url,
            ];
        }

        if (count($attachments['files']) || count($attachments['links'])) {
            $ticket->update([
                'attachments' => $attachments,
            ]);
        }

        foreach (array_values($validated['checklistItems'] ?? []) as $i => $row) {
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            TicketChecklistItem::create([
                'ticket_id' => $ticket->id,
                'title' => $title,
                'assigned_to' => ! empty($row['assigned_to']) ? (int) $row['assigned_to'] : null,
                'due_date' => ! empty($row['due_date']) ? $row['due_date'] : null,
                'sort_order' => $i,
            ]);
        }

        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);

        AutomationService::evaluate($ticket, 'ticket_created');

        $ticket->load(['category', 'priority', 'group', 'creator', 'assignees']);
        WebhookService::dispatch($orgId, 'ticket.created', [
            'ticket' => (new \App\Http\Resources\Api\V1\TicketResource($ticket))->resolve(),
        ]);

        // Notify the ticket creator with a confirmation email
        $currentOrg = request()->attributes->get('currentOrganization');
        $orgName = $currentOrg?->name ?? config('app.name', 'Support');
        $user->notify(new TicketCreatedNotification(
            ticketId: $ticket->id,
            ticketPublicId: $ticket->public_id,
            ticketReference: $ticket->shortReference(),
            ticketSubject: $ticket->subject,
            organizationId: $orgId,
            organizationName: $orgName,
        ));
        event(new UserNotificationReceived((int) $user->id, 'ticket_created'));

        $this->redirectRoute('tickets.index');
    }

    public function addChecklistItem(): void
    {
        $this->checklistItems[] = ['title' => '', 'assigned_to' => null, 'due_date' => null];
    }

    public function removeChecklistItem(int $index): void
    {
        $idx = (int) $index;
        if ($idx >= 0 && $idx < count($this->checklistItems)) {
            array_splice($this->checklistItems, $idx, 1);
        }
    }

    public function moveChecklistItemUp(int $index): void
    {
        $idx = (int) $index;
        if ($idx <= 0 || $idx >= count($this->checklistItems)) {
            return;
        }
        $tmp = $this->checklistItems[$idx];
        $this->checklistItems[$idx] = $this->checklistItems[$idx - 1];
        $this->checklistItems[$idx - 1] = $tmp;
    }

    public function moveChecklistItemDown(int $index): void
    {
        $idx = (int) $index;
        if ($idx < 0 || $idx >= count($this->checklistItems) - 1) {
            return;
        }
        $tmp = $this->checklistItems[$idx];
        $this->checklistItems[$idx] = $this->checklistItems[$idx + 1];
        $this->checklistItems[$idx + 1] = $tmp;
    }

    public function addLink(): void
    {
        $url = trim($this->linkUrl);
        if ($url === '') {
            return;
        }

        $this->validate([
            'linkUrl' => ['url', 'max:2000'],
        ]);

        if (! in_array($url, $this->links, true)) {
            $this->links[] = $url;
        }

        $this->linkUrl = '';
    }

    public function removeLink(int $index): void
    {
        unset($this->links[$index]);
        $this->links = array_values($this->links);
    }

    public function removeFile(int $index): void
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files);
    }

    public function render()
    {
        $orgId = (int) session('current_organization_id');

        $categories = $orgId
            ? Cache::remember(CacheHelper::categoriesKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
                return TicketCategory::query()
                    ->where('organization_id', $orgId)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();
            })
            : collect();

        $priorities = $orgId
            ? Cache::remember(CacheHelper::prioritiesKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
                return TicketPriority::query()
                    ->where('organization_id', $orgId)
                    ->where('is_active', true)
                    ->orderBy('level')
                    ->get();
            })
            : collect();

        $assignees = ($this->canAssignAtCreate && $orgId)
            ? Cache::remember("create_ticket_assignees_staff:{$orgId}", CacheHelper::TTL, function () use ($orgId) {
                return User::query()
                    ->assignableInOrganization($orgId)
                    ->orderBy('name')
                    ->get(['id', 'name', 'email']);
            })
            : collect();

        $ticketGroups = $orgId
            ? Cache::remember(CacheHelper::ticketGroupsKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
                return TicketGroup::query()
                    ->where('organization_id', $orgId)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name', 'color']);
            })
            : collect();

        $userId = (int) (Auth::id() ?: 0);
        $form = ($orgId && $userId && $this->ticket_category_id)
            ? $this->resolveForm($orgId, (int) $this->ticket_category_id, $userId)
            : null;
        $formFields = $form?->fields ?? collect();

        return view('livewire.tickets.create', [
            'categories' => $categories,
            'priorities' => $priorities,
            'ticketGroups' => $ticketGroups,
            'assignees' => $assignees,
            'formTemplateName' => $form?->name,
            'formSteps' => collect(),
            'formFields' => $formFields,
            'canAssignAtCreate' => $this->canAssignAtCreate,
        ]);
    }

    private function resolveForm(int $orgId, int $categoryId, int $userId): ?Form
    {
        return Cache::remember(
            "create_ticket_form:{$orgId}:{$categoryId}:{$userId}",
            CacheHelper::TTL,
            function () use ($orgId, $categoryId, $userId) {
                // Single query with specificity ranking:
                // 1) category+user, 2) category+public, 3) global+user, 4) global+public.
                $form = Form::query()
                    ->where('organization_id', $orgId)
                    ->where('status', FormStatus::Published)
                    ->where(function ($q) use ($categoryId) {
                        $q->whereNull('ticket_category_id')
                            ->orWhere('ticket_category_id', $categoryId);
                    })
                    ->where(function ($q) use ($userId) {
                        $q->whereNull('target_user_id')
                            ->orWhere('target_user_id', $userId);
                    })
                    ->orderByRaw(
                        'CASE
                            WHEN ticket_category_id = ? AND target_user_id = ? THEN 4
                            WHEN ticket_category_id = ? AND target_user_id IS NULL THEN 3
                            WHEN ticket_category_id IS NULL AND target_user_id = ? THEN 2
                            ELSE 1
                        END DESC',
                        [$categoryId, $userId, $categoryId, $userId]
                    )
                    ->with(['fields'])
                    ->first();
                if ($form) {
                    return $form;
                }

                // Last resort: category's default_form_id
                $category = TicketCategory::query()->find($categoryId);
                if ($category && $category->default_form_id) {
                    return Form::query()
                        ->where('id', $category->default_form_id)
                        ->where('organization_id', $orgId)
                        ->where('status', FormStatus::Published)
                        ->with(['fields'])
                        ->first();
                }

                return null;
            }
        );
    }
}
