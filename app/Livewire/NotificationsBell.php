<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsBell extends Component
{
    public bool $open = false;

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

    public function getUnreadCountProperty(): int
    {
        $user = Auth::user();
        if (! $user) {
            return 0;
        }

        return $user->unreadNotifications()->count();
    }

    public function getNotificationsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();
        if (! $user) {
            return collect();
        }

        return $user->notifications()->latest()->limit(20)->get(['id', 'type', 'data', 'read_at', 'created_at']);
    }

    public function markAsRead(string $id): void
    {
        Auth::user()?->unreadNotifications()->where('id', $id)->first()?->markAsRead();
    }

    public function markAllAsRead(): void
    {
        Auth::user()?->unreadNotifications()->update(['read_at' => now()]);
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
        if ($this->open) {
            $this->dispatch('notifications-opened');
        }
    }

    public function render(): View
    {
        return view('livewire.notifications-bell');
    }
}
