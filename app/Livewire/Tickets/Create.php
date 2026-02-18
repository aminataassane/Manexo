<?php

namespace App\Livewire\Tickets;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketChecklistItem;
use App\Models\TicketFormTemplate;
use App\Models\TicketPriority;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public int $ticket_category_id;
    public int $ticket_priority_id;
    public ?int $assigned_to = null;
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

    public function mount(): void
    {
        $orgId = (int) session('current_organization_id');

        // Preselect first active options if available
        if ($orgId) {
            $category = TicketCategory::query()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('name')
                ->first();

            $priority = TicketPriority::query()
                ->where('organization_id', $orgId)
                ->where('is_active', true)
                ->orderBy('level')
                ->first();

            if ($category) {
                $this->ticket_category_id = $category->id;
            }

            if ($priority) {
                $this->ticket_priority_id = $priority->id;
            }
        }
    }

    public function submit(): void
    {
        $user = Auth::user();
        $orgId = (int) session('current_organization_id');

        if (! $user || ! $orgId) {
            $this->redirectRoute('organizations.select');
            return;
        }

        $template = $this->resolveTemplate($orgId, (int) ($this->ticket_category_id ?? 0), (int) $user->id);
        $fields = $template?->fields ?? collect();

        $dynamicRules = [];
        foreach ($fields as $f) {
            $path = "custom.{$f->key}";
            $rules = [$f->required ? 'required' : 'nullable'];

            $type = (string) $f->type;
            if ($type === 'email') {
                $rules[] = 'email';
                $rules[] = 'max:255';
            } elseif ($type === 'number') {
                $rules[] = 'numeric';
            } elseif ($type === 'date') {
                $rules[] = 'date';
            } elseif ($type === 'checkbox') {
                $rules[] = 'boolean';
            } elseif ($type === 'textarea') {
                $rules[] = 'string';
                $rules[] = 'max:5000';
            } elseif ($type === 'select') {
                $rules[] = 'string';
                $rules[] = 'max:120';
                if (is_array($f->options) && count($f->options)) {
                    $rules[] = Rule::in($f->options);
                }
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
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'files' => ['array', 'max:5'],
            'files.*' => ['file', 'max:10240'],
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

        if (! $categoryOk || ! $priorityOk) {
            $this->addError('ticket_category_id', "Sélection invalide pour l'entreprise.");
            return;
        }

        // Optional: ensure assignee belongs to current org
        if (($validated['assigned_to'] ?? null) !== null) {
            $assigneeOk = User::query()
                ->whereKey((int) $validated['assigned_to'])
                ->whereHas('organizations', fn($q) => $q->whereKey($orgId))
                ->exists();

            if (! $assigneeOk) {
                $this->addError('assigned_to', "Assigné invalide pour l'entreprise.");
                return;
            }
        }

        $customFields = [];
        foreach ($fields as $f) {
            $key = (string) $f->key;
            $val = $this->custom[$key] ?? null;
            if ($f->type === 'checkbox') {
                $val = (bool) $val;
            }
            if (is_string($val)) {
                $val = trim($val);
            }

            // Keep false/0, ignore empty strings/null
            if ($val === null || $val === '') {
                continue;
            }
            $customFields[$key] = $val;
        }

        $ticket = Ticket::create([
            'organization_id' => $orgId,
            'created_by' => $user->id,
            'ticket_category_id' => $validated['ticket_category_id'],
            'ticket_priority_id' => $validated['ticket_priority_id'],
            'assigned_to' => $validated['assigned_to'] ?? null,
            'status' => TicketStatus::Open,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'custom_fields' => $customFields ?: null,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        $attachments = [
            'files' => [],
            'links' => [],
        ];

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        foreach (($validated['files'] ?? []) as $file) {
            $original = (string) ($file->getClientOriginalName() ?: 'file');
            $ext = (string) ($file->getClientOriginalExtension() ?: '');
            $safeBase = Str::slug(pathinfo($original, PATHINFO_FILENAME)) ?: 'file';
            $filename = $safeBase . '-' . Str::lower(Str::random(10)) . ($ext ? '.' . $ext : '');

            $path = $file->storeAs("ticket-attachments/org-{$orgId}/ticket-{$ticket->id}", $filename, 'public');

            $attachments['files'][] = [
                'disk' => 'public',
                'path' => $path,
                'name' => $original,
                'size' => method_exists($file, 'getSize') ? (int) $file->getSize() : null,
                'mime' => method_exists($file, 'getMimeType') ? (string) $file->getMimeType() : null,
                'url' => $disk->url($path),
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
            ? TicketCategory::query()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            : collect();

        $priorities = $orgId
            ? TicketPriority::query()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->orderBy('level')
            ->get()
            : collect();

        $assignees = $orgId
            ? User::query()
            ->whereHas('organizations', fn($q) => $q->whereKey($orgId))
            ->orderBy('name')
            ->get()
            : collect();

        $userId = (int) (Auth::id() ?: 0);
        $template = ($orgId && $userId && ($this->ticket_category_id ?? null))
            ? $this->resolveTemplate($orgId, (int) $this->ticket_category_id, $userId)
            : null;
        $formSteps = $template?->steps ?? collect();
        $formFields = $template?->fields ?? collect();

        return view('livewire.tickets.create', [
            'categories' => $categories,
            'priorities' => $priorities,
            'assignees' => $assignees,
            'formTemplateName' => $template?->name,
            'formSteps' => $formSteps,
            'formFields' => $formFields,
        ]);
    }

    private function resolveTemplate(int $orgId, int $categoryId, int $userId): ?TicketFormTemplate
    {
        $base = TicketFormTemplate::query()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->where(function ($q) use ($categoryId) {
                $q->whereNull('ticket_category_id');
                if ($categoryId) {
                    $q->orWhere('ticket_category_id', $categoryId);
                }
            })
            ->where(function ($q) use ($userId) {
                $q->whereNull('target_user_id');
                if ($userId) {
                    $q->orWhere('target_user_id', $userId);
                }
            });

        // Prefer: specific user + specific category
        $first = (clone $base)
            ->where('ticket_category_id', $categoryId)
            ->where('target_user_id', $userId)
            ->with(['steps.fields', 'fields'])
            ->first();
        if ($first) {
            return $first;
        }

        // Prefer: public + specific category
        $second = (clone $base)
            ->where('ticket_category_id', $categoryId)
            ->whereNull('target_user_id')
            ->with(['steps.fields', 'fields'])
            ->first();
        if ($second) {
            return $second;
        }

        // Prefer: specific user + all categories
        $third = (clone $base)
            ->whereNull('ticket_category_id')
            ->where('target_user_id', $userId)
            ->with(['steps.fields', 'fields'])
            ->first();
        if ($third) {
            return $third;
        }

        // Fallback: public + all categories
        return (clone $base)
            ->whereNull('ticket_category_id')
            ->whereNull('target_user_id')
            ->with(['steps.fields', 'fields'])
            ->first();
    }
}
