<?php

namespace App\Notifications;

use App\Models\Organization;
use App\Models\OrganizationInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrganizationInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public OrganizationInvitation $invitation,
        public Organization $organization,
    ) {}

    public function via(object $notifiable): array
    {
        if ($notifiable instanceof \App\Models\User) {
            return ['database', 'mail'];
        }

        return ['mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'organization_invitation',
            'organization_name' => $this->organization->name,
            'invitation_token' => $this->invitation->token,
            'inviter_name' => $this->invitation->inviter?->name,
            'role' => $this->invitation->role,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $inviterName = $this->invitation->inviter?->name ?? 'Un administrateur';
        $orgName = $this->organization->name;
        $roleName = $this->invitation->role;
        $acceptUrl = route('invitations.accept', ['token' => $this->invitation->token]);
        $appName = config('app.name', 'Manexo');

        return (new MailMessage())
            ->subject(__('invitations.email_subject', ['org' => $orgName]))
            ->view('emails.organization-invitation', [
                'appName' => $appName,
                'logoUrl' => asset('assets/Logo(1).png'),
                'orgName' => $orgName,
                'roleName' => $roleName,
                'inviterName' => $inviterName,
                'acceptUrl' => $acceptUrl,
                'expiresAt' => $this->invitation->expires_at,
            ]);
    }
}
