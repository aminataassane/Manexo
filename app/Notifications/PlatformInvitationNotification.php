<?php

namespace App\Notifications;

use App\Models\PlatformInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlatformInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PlatformInvitation $invitation,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $inviterName = $this->invitation->inviter?->name ?? 'Un administrateur';
        $roleName = $this->invitation->platform_role->label();
        $acceptUrl = route('platform-invitations.accept', ['token' => $this->invitation->token]);
        $appName = config('app.name', 'Manexo');

        return (new MailMessage())
            ->subject(__('platform_invitations.email_subject'))
            ->view('emails.platform-invitation', [
                'appName' => $appName,
                'roleName' => $roleName,
                'inviterName' => $inviterName,
                'acceptUrl' => $acceptUrl,
                'expiresAt' => $this->invitation->expires_at,
            ]);
    }
}
