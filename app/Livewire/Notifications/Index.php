<?php

namespace App\Livewire\Notifications;

use App\Helpers\CacheHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.manexo-app')]
#[Title('Notifications')]
class Index extends Component
{
    use WithPagination;

    public bool $ready = true;

    public function loadPage(): void {}

    #[Url]
    public string $filter = 'all';

    #[Url]
    public string $category = 'all';

    public int $perPage = 20;

    /**
     * @var array<string, array<int, string>>
     */
    private const CATEGORY_TYPES = [
        'tickets' => ['ticket_new_message', 'ticket_assignee', 'ticket_mention', 'ticket_reopened'],
        'discussions' => ['discussion_new_message', 'discussion_invite', 'discussion_removed'],
        'forms' => ['form_assignment', 'form_response', 'form_overdue'],
        'reports' => ['task_report_shared'],
        'team' => ['organization_invitation', 'invitation_accepted', 'team_role_changed'],
    ];

    private function orgId(): int
    {
        return (int) session('current_organization_id');
    }

    /**
     * Scope notifications to the currently selected organization.
     * Works with legacy payloads that may not include organization_id.
     */
    private function applyOrganizationScope($query, int $orgId)
    {
        if ($orgId <= 0) {
            return $query->whereRaw('1 = 0');
        }

        $driver = DB::connection()->getDriverName();
        if ($driver !== 'pgsql') {
            // Fallback (non-pgsql): safest option is explicit org fields only.
            return $query->where(function ($q) use ($orgId) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(notifications.data, '$.organization_id')) = ?", [(string) $orgId])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(notifications.data, '$.org_id')) = ?", [(string) $orgId]);
            });
        }

        $ticketTypes = [
            'ticket_new_message', 'ticket_assignee', 'ticket_mention', 'ticket_reopened',
            'ticket_created', 'ticket_status_changed', 'ticket_satisfaction_request',
            'ticket_client_routing', 'sla_at_risk', 'sla_breached',
            'approval_requested', 'approval_decision', 'checklist_item_assigned',
        ];

        return $query->where(function ($scope) use ($orgId, $ticketTypes) {
            // Direct org keys in payload (preferred path for new notifications).
            $scope->whereRaw("((notifications.data::jsonb->>'organization_id') ~ '^[0-9]+$' and ((notifications.data::jsonb->>'organization_id')::bigint = ?))", [$orgId])
                ->orWhereRaw("((notifications.data::jsonb->>'org_id') ~ '^[0-9]+$' and ((notifications.data::jsonb->>'org_id')::bigint = ?))", [$orgId]);

            // Ticket-linked notifications (legacy + current payloads).
            $scope->orWhere(function ($q) use ($orgId, $ticketTypes) {
                $q->whereIn(DB::raw("(notifications.data::jsonb->>'type')"), $ticketTypes)
                    ->whereRaw("(notifications.data::jsonb->>'ticket_id') ~ '^[0-9]+$'")
                    ->whereExists(function ($sub) use ($orgId) {
                        $sub->selectRaw('1')
                            ->from('tickets')
                            ->whereRaw("tickets.id = ((notifications.data::jsonb->>'ticket_id')::bigint)")
                            ->where('tickets.organization_id', $orgId);
                    });
            });

            // Discussion-linked notifications.
            $scope->orWhere(function ($q) use ($orgId) {
                $q->whereIn(DB::raw("(notifications.data::jsonb->>'type')"), ['discussion_new_message', 'discussion_invite', 'discussion_removed'])
                    ->whereRaw("(notifications.data::jsonb->>'thread_id') ~ '^[0-9]+$'")
                    ->whereExists(function ($sub) use ($orgId) {
                        $sub->selectRaw('1')
                            ->from('discussion_threads')
                            ->whereRaw("discussion_threads.id = ((notifications.data::jsonb->>'thread_id')::bigint)")
                            ->where('discussion_threads.organization_id', $orgId);
                    });
            });

            // Form assignment/overdue linked by assignment_id.
            $scope->orWhere(function ($q) use ($orgId) {
                $q->whereIn(DB::raw("(notifications.data::jsonb->>'type')"), ['form_assignment', 'form_overdue'])
                    ->whereRaw("(notifications.data::jsonb->>'assignment_id') ~ '^[0-9]+$'")
                    ->whereExists(function ($sub) use ($orgId) {
                        $sub->selectRaw('1')
                            ->from('form_assignments')
                            ->whereRaw("form_assignments.id = ((notifications.data::jsonb->>'assignment_id')::bigint)")
                            ->where('form_assignments.organization_id', $orgId);
                    });
            });

            // Form response linked by form_id.
            $scope->orWhere(function ($q) use ($orgId) {
                $q->whereRaw("(notifications.data::jsonb->>'type') = 'form_response'")
                    ->whereRaw("(notifications.data::jsonb->>'form_id') ~ '^[0-9]+$'")
                    ->whereExists(function ($sub) use ($orgId) {
                        $sub->selectRaw('1')
                            ->from('forms')
                            ->whereRaw("forms.id = ((notifications.data::jsonb->>'form_id')::bigint)")
                            ->where('forms.organization_id', $orgId);
                    });
            });

            // Organization invitation (legacy): scoped through invitation token.
            $scope->orWhere(function ($q) use ($orgId) {
                $q->whereRaw("(notifications.data::jsonb->>'type') = 'organization_invitation'")
                    ->whereRaw("coalesce((notifications.data::jsonb->>'invitation_token'), '') <> ''")
                    ->whereExists(function ($sub) use ($orgId) {
                        $sub->selectRaw('1')
                            ->from('organization_invitations')
                            ->whereRaw("organization_invitations.token = (notifications.data::jsonb->>'invitation_token')")
                            ->where('organization_invitations.organization_id', $orgId);
                    });
            });
        });
    }

    public function getListeners(): array
    {
        $userId = Auth::id();

        if (! $userId) {
            return [];
        }

        return [
            "echo-private:App.Models.User.{$userId},.notification.received" => '$refresh',
        ];
    }

    public function getNotificationsProperty(): LengthAwarePaginator
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
        $orgId = $this->orgId();

        $query = $user->notifications();
        $query = $this->applyOrganizationScope($query, $orgId);

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        }

        if ($this->category !== 'all') {
            $types = self::CATEGORY_TYPES[$this->category] ?? [];
            if ($types === []) {
                $this->category = 'all';
            } else {
                $query->whereIn(DB::raw("(data::jsonb->>'type')"), $types);
            }
        }

        return $query->latest()->paginate($this->perPage);
    }

    public function getUnreadCountProperty(): int
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user) {
            return 0;
        }
        $orgId = $this->orgId();

        $query = $user->unreadNotifications();
        $query = $this->applyOrganizationScope($query, $orgId);

        return (int) $query->count();
    }

    public function updatedFilter(string $value): void
    {
        if (! in_array($value, ['all', 'unread'], true)) {
            $this->filter = 'all';
        }
        $this->resetPage();
    }

    public function updatedCategory(string $value): void
    {
        $allowed = array_merge(['all'], array_keys(self::CATEGORY_TYPES));
        if (! in_array($value, $allowed, true)) {
            $this->category = 'all';
        }
        $this->resetPage();
    }

    public function setFilter(string $filter): void
    {
        $nextFilter = in_array($filter, ['all', 'unread'], true) ? $filter : 'all';
        if ($nextFilter === $this->filter) {
            return;
        }
        $this->filter = $nextFilter;
        $this->resetPage();
    }

    public function setCategory(string $category): void
    {
        $allowed = array_merge(['all'], array_keys(self::CATEGORY_TYPES));
        $nextCategory = in_array($category, $allowed, true) ? $category : 'all';
        if ($nextCategory === $this->category) {
            return;
        }
        $this->category = $nextCategory;
        $this->resetPage();
    }

    public function markAsRead(string $id): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            $orgId = $this->orgId();
            $query = $user->unreadNotifications()->where('id', $id);
            $query = $this->applyOrganizationScope($query, $orgId);
            $query->update(['read_at' => now()]);
            CacheHelper::invalidateNotificationsCount((int) $user->id);
            CacheHelper::invalidateSidebarDiscussionsUnread((int) $user->id);
        }
    }

    public function markAllAsRead(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            $orgId = $this->orgId();
            $query = $user->unreadNotifications();
            $query = $this->applyOrganizationScope($query, $orgId);
            $query->update(['read_at' => now()]);
            CacheHelper::invalidateNotificationsCount((int) $user->id);
            CacheHelper::invalidateSidebarDiscussionsUnread((int) $user->id);
        }
    }

    public function render(): View
    {
        $notifications = $this->notifications;
        $unreadCount = $this->unreadCount;

        return view('livewire.notifications.index', compact('notifications', 'unreadCount'));
    }
}
