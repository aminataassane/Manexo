<?php

namespace App\Livewire\Notifications;

use App\Helpers\CacheHelper;
use App\Support\NotificationOrganizationScope;
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
        $query = NotificationOrganizationScope::apply($query, $orgId);

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
        $query = NotificationOrganizationScope::apply($query, $orgId);

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
            $query = NotificationOrganizationScope::apply($query, $orgId);
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
            $query = NotificationOrganizationScope::apply($query, $orgId);
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
