<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordBiocolis extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('🔑 Réinitialisation de votre mot de passe Biocolis')
            ->view('emails.auth.reset-password', [
                'url'  => $url,
                'user' => $notifiable,
            ]);
    }
}



