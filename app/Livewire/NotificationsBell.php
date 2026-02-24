<?php

namespace App\Livewire;

use App\Helpers\CacheHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class NotificationsBell extends Component
{
    public bool $open = false;

    /** Charge la liste des notifications uniquement à l'ouverture du dropdown (évite la requête à chaque page). */
    public bool $notificationsLoaded = false;

    public function getListeners(): array
    {
        $userId = Auth::id();

        if (! $userId) {
            return [];
        }

        return [
            "echo-private:App.Models.User.{$userId},.notification.received" => 'invalidateNotificationsCache',
        ];
    }

    public function invalidateNotificationsCache(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $this->dispatch('$refresh');
        if ($user) {
            CacheHelper::invalidateNotificationsCount((int) $user->id);
            CacheHelper::invalidateSidebarDiscussionsUnread((int) $user->id);
        }
    }

    public function getUnreadCountProperty(): int
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user) {
            return 0;
        }

        $userId = (int) $user->id;

        return (int) Cache::remember(
            CacheHelper::notificationsUnreadCountKey($userId),
            CacheHelper::TTL_SHORT,
            fn () => $user->unreadNotifications()->count()
        );
    }

    public function getNotificationsProperty(): EloquentCollection
    {
        if (! $this->notificationsLoaded) {
            return new EloquentCollection([]);
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (! $user) {
            return new EloquentCollection([]);
        }

        return $user->notifications()->latest()->limit(20)->get(['id', 'type', 'data', 'read_at', 'created_at']);
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

    public function toggle(): void
    {
        $this->open = ! $this->open;
        if ($this->open) {
            $this->notificationsLoaded = true;
            $this->dispatch('notifications-opened');
        }
    }

    public function render(): View
    {
        return view('livewire.notifications-bell');
    }
}
