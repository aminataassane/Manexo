<?php

namespace App\Livewire\Tickets;

use App\Enums\TicketStatus;
use App\Helpers\CacheHelper;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Services\ApprovalService;
use App\Services\AutomationService;
use App\Services\SlaService;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateForm extends Component
{
    use WithFileUploads;

    public bool $ready = true;

    public function loadPage(): void {}

    public int $ticket_category_id;

    public int $ticket_priority_id;

    public string $subject = '';

    public string $description = '';

    /** @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $files = [];

    /** @var array<int, string> */
    public array $links = [];

    public string $linkUrl = '';

    /** KB deflection suggestions */
    public array $kbSuggestions = [];

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
        $cacheKey = 'create_drawer:kb_suggestions:'.$orgId.':'.md5(mb_strtolower($needle));
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
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'title' => $a->title,
                    'excerpt' => $a->excerpt(200),
                    'view_count' => $a->view_count,
                ])
                ->toArray();
        });
    }

    public function mount(): void
    {
        $orgId = (int) session('current_organization_id');

        if ($orgId) {
            $category = Cache::remember(CacheHelper::categoriesKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
                return TicketCategory::query()
                    ->where('organization_id', $orgId)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id'])
                    ->first();
            });

            $priority = Cache::remember(CacheHelper::prioritiesKey($orgId, true), CacheHelper::TTL, function () use ($orgId) {
                return TicketPriority::query()
                    ->where('organization_id', $orgId)
                    ->where('is_active', true)
                    ->orderByDesc('level')
                    ->get(['id'])
                    ->first();
            });

            if ($category) {
                $this->ticket_category_id = (int) $category->id;
            }

            if ($priority) {
                $this->ticket_priority_id = (int) $priority->id;
            }
        }
    }

    public function submit(): void
    {
        $user = Auth::user();
        $orgId = (int) session('current_organization_id');

        if (! $user || ! $orgId) {
            $this->dispatch('tickets:closeCreateDrawer');

            return;
        }

        abort_if(! ($user instanceof \App\Models\User) || ! $user->hasPermission(\App\Enums\Permission::TicketsCreate), 403);

        $validated = $this->validate([
            'ticket_category_id' => ['required', 'integer', 'exists:ticket_categories,id'],
            'ticket_priority_id' => ['required', 'integer', 'exists:ticket_priorities,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'files' => ['array', 'max:5'],
            'files.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt,zip'], // 10MB each
            'links' => ['array', 'max:5'],
            'links.*' => ['url', 'max:2000'],
        ]);

        $categoryOk = TicketCategory::query()
            ->where('id', $validated['ticket_category_id'])
            ->where('organization_id', $orgId)
            ->exists();

        $priorityOk = TicketPriority::query()
            ->where('id', $validated['ticket_priority_id'])
            ->where('organization_id', $orgId)
            ->exists();

        if (! $categoryOk || ! $priorityOk) {
            $this->addError('ticket_category_id', "Sélection invalide pour l'entreprise.");

            return;
        }

        $ticket = Ticket::create([
            'organization_id' => $orgId,
            'created_by' => $user->id,
            'ticket_category_id' => $validated['ticket_category_id'],
            'ticket_priority_id' => $validated['ticket_priority_id'],
            'status' => TicketStatus::Open,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
        ]);

        SlaService::applyPolicy($ticket);
        ApprovalService::applyPolicy($ticket);

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

        $this->reset(['subject', 'description', 'files', 'links', 'linkUrl']);

        CacheHelper::invalidateDashboard($orgId);
        CacheHelper::invalidateReports($orgId);
        CacheHelper::invalidateTicketCounts($orgId);

        AutomationService::evaluate($ticket, 'ticket_created');

        $ticket->load(['category', 'priority', 'group', 'creator', 'assignees']);
        WebhookService::dispatch($orgId, 'ticket.created', [
            'ticket' => (new \App\Http\Resources\Api\V1\TicketResource($ticket))->resolve(),
        ]);

        $this->dispatch('tickets:created');
        $this->dispatch('tickets:closeCreateDrawer');
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
                    ->orderByDesc('level')
                    ->get();
            })
            : collect();

        return view('livewire.tickets.create-form', [
            'categories' => $categories,
            'priorities' => $priorities,
        ]);
    }
}
