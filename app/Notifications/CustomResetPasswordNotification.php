<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPassword
{
    public function __construct(string $token)
    {
        parent::__construct($token);
    }

    public function toMail($notifiable)
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:3000');
        $resetUrl = "{$frontendUrl}/reset-password/{$this->token}?email={$notifiable->email}";

        return (new MailMessage)
            ->subject('Reset Your Password | ' . config('app.name'))
            ->markdown('Admin.Mail.password-reset-link', [
                'url' => $resetUrl,
                'name' => $notifiable->name,
            ]);
    }
}
