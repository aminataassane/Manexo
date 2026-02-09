<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordManexoNotification extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $email = method_exists($notifiable, 'getEmailForPasswordReset')
            ? $notifiable->getEmailForPasswordReset()
            : ($notifiable->email ?? null);

        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $email,
        ]);

        $expiresMinutes = (int) config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe Manexo')
            ->view('emails.auth.password-reset', [
                'appName' => config('app.name', 'Manexo'),
                'toEmail' => $email,
                'resetUrl' => $resetUrl,
                'expiresMinutes' => $expiresMinutes,
            ]);
    }
}

