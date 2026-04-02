<?php

namespace App\Livewire;

use App\Helpers\CacheHelper;
use App\Models\User;
use App\Notifications\DiscussionInviteNotification;
use App\Notifications\DiscussionNewMessageNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class NotificationsBell extends Component
{
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

    /**
     * Refresh counters when a realtime notification is received, and show a toast preview.
     *
     * @param  array<string, mixed>  $payload
     */
    public function invalidateNotificationsCache(array $payload = []): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user instanceof User) {
            CacheHelper::invalidateNotificationsCount((int) $user->id);
            CacheHelper::invalidateSidebarDiscussionsUnread((int) $user->id);
            $this->pushSidebarBadgeCountsToBrowser($user);
        }

        $this->dispatch('$refresh');

        $notificationType = (string) ($payload['type'] ?? 'general');
        $message = $this->notificationPreviewMessage($notificationType);
        if ($message !== '') {
            $this->dispatch('toast', type: 'info', message: $message);
        }
    }

    /**
     * File d’attente désactivée ou WebSocket coupé : garde cloche + badges sidebar à jour.
     */
    public function refreshBellAndSidebarBadges(): void
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            return;
        }
        CacheHelper::invalidateNotificationsCount((int) $user->id);
        CacheHelper::invalidateSidebarDiscussionsUnread((int) $user->id);
        $this->pushSidebarBadgeCountsToBrowser($user);
    }

    private function pushSidebarBadgeCountsToBrowser(User $user): void
    {
        $notifications = (int) $user->unreadNotifications()->count();
        $discussions = (int) $user->unreadNotifications()
            ->whereIn('type', [
                DiscussionNewMessageNotification::class,
                DiscussionInviteNotification::class,
            ])
            ->count();

        $this->js('window.dispatchEvent(new CustomEvent("manexo-sidebar-badges",{detail:{notifications:'.$notifications.',discussions:'.$discussions.'}}))');
    }

    private function notificationPreviewMessage(string $type): string
    {
        return match ($type) {
            'ticket_new_message' => __('Nouveau message sur un ticket'),
            'ticket_mention' => __('Vous avez ete mentionne dans un ticket'),
            'ticket_assignee' => __('Mise a jour de votre assignation ticket'),
            'discussion_invite' => __('Nouvelle invitation de discussion'),
            'discussion_new_message' => __('Nouveau message dans une discussion'),
            'discussion_removed' => __('Vous avez ete retire d une discussion'),
            'form_assignment' => __('Nouveau formulaire assigne'),
            'form_response' => __('Nouvelle reponse de formulaire'),
            'form_overdue' => __('Un formulaire est en retard'),
            'checklist_item_assigned' => __('Nouvel element de checklist assigne'),
            'invitation_accepted' => __('Invitation d equipe acceptee'),
            'team_role_changed' => __('Votre role d equipe a ete modifie'),
            'ticket_reopened' => __('Un ticket a ete rouvert'),
            default => __('Nouvelle notification'),
        };
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
        /** @var User|null $user */
        $user = Auth::user();
        if ($user instanceof User) {
            $user->unreadNotifications()->where('id', $id)->first()?->markAsRead();
            CacheHelper::invalidateNotificationsCount((int) $user->id);
            CacheHelper::invalidateSidebarDiscussionsUnread((int) $user->id);
            $this->pushSidebarBadgeCountsToBrowser($user);
        }
    }

    public function markAllAsRead(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user instanceof User) {
            $user->unreadNotifications()->update(['read_at' => now()]);
            CacheHelper::invalidateNotificationsCount((int) $user->id);
            CacheHelper::invalidateSidebarDiscussionsUnread((int) $user->id);
            $this->pushSidebarBadgeCountsToBrowser($user);
        }
    }

    /** Called from Alpine when the dropdown opens — loads notifications on first open. */
    public function loadNotifications(): void
    {
        $this->notificationsLoaded = true;
    }

    public function render(): View
    {
        return view('livewire.notifications-bell');
    }
}
