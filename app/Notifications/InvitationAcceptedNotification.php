<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $acceptedByName,
        public string $acceptedByEmail,
        public string $organizationName,
        public int $organizationId,
        public string $role,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'invitation_accepted',
            'accepted_by_name' => $this->acceptedByName,
            'accepted_by_email' => $this->acceptedByEmail,
            'organization_name' => $this->organizationName,
            'organization_id' => $this->organizationId,
            'role' => $this->role,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'Manexo');

        return (new MailMessage())
            ->subject(__('invitations.accepted_email_subject', ['name' => $this->acceptedByName, 'org' => $this->organizationName]))
            ->view('emails.invitation-accepted', [
                'appName' => $appName,
                'logoUrl' => asset('assets/Logo(1).png'),
                'acceptedByName' => $this->acceptedByName,
                'acceptedByEmail' => $this->acceptedByEmail,
                'organizationName' => $this->organizationName,
                'roleName' => $this->role,
                'dashboardUrl' => route('admin.users'),
            ]);
    }
}
