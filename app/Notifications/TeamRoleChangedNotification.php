<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class TeamRoleChangedNotification extends Notification
{
    public function __construct(
        public int $organizationId,
        public string $organizationName,
        public string $oldRole,
        public string $newRole,
        public string $changedByName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'team_role_changed',
            'organization_id' => $this->organizationId,
            'organization_name' => $this->organizationName,
            'old_role' => $this->oldRole,
            'new_role' => $this->newRole,
            'changed_by_name' => $this->changedByName,
            'body_excerpt' => __('Votre rôle dans l\'équipe a été mis à jour.'),
        ];
    }
}
