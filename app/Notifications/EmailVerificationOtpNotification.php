<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $code,
        public int $expiresMinutes = 10,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre code de vérification Manexo')
            ->view('emails.auth.email-verification-otp', [
                'code' => $this->code,
                'expiresMinutes' => $this->expiresMinutes,
                'appName' => config('app.name', 'Manexo'),
                'logoUrl' => asset('assets/logo.png'),
                'toEmail' => method_exists($notifiable, 'getEmailForVerification')
                    ? $notifiable->getEmailForVerification()
                    : ($notifiable->email ?? null),
            ]);
    }
}
