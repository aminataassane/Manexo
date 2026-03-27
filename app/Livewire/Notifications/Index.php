<?php

namespace App\Livewire\Notifications;

use App\Helpers\CacheHelper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
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

        $query = $user->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        }

        if ($this->category !== 'all') {
            $types = self::CATEGORY_TYPES[$this->category] ?? [];
            if ($types === []) {
                $this->category = 'all';
            } else {
                $query->where(function ($q) use ($types) {
                    foreach ($types as $type) {
                        $q->orWhereRaw("(data::jsonb->>'type') = ?", [$type]);
                    }
                });
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

        return $user->unreadNotifications()->count();
    }

    public function setFilter(string $filter): void
    {
        $this->filter = in_array($filter, ['all', 'unread']) ? $filter : 'all';
        $this->resetPage();
    }

    public function setCategory(string $category): void
    {
        $allowed = array_merge(['all'], array_keys(self::CATEGORY_TYPES));
        $this->category = in_array($category, $allowed, true) ? $category : 'all';
        $this->resetPage();
    }

    public function markAsRead(string $id): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications()->where('id', $id)->first()?->markAsRead();
            CacheHelper::invalidateNotificationsCount((int) $user->id);
            CacheHelper::invalidateSidebarDiscussionsUnread((int) $user->id);
        }
    }

    public function markAllAsRead(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications()->update(['read_at' => now()]);
            CacheHelper::invalidateNotificationsCount((int) $user->id);
            CacheHelper::invalidateSidebarDiscussionsUnread((int) $user->id);
        }
    }

    public function render(): View
    {
        return view('livewire.notifications.index');
    }
}
