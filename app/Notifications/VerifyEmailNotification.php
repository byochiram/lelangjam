<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends BaseVerifyEmail
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tempus Auctions - Verifikasi Email')
            ->greeting('Halo ' . ($notifiable->name ?: ''))
            ->line('Terima kasih telah mendaftar di Tempus Auctions.')
            ->line('Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.')
            ->action('Verifikasi Email', $this->verificationUrl($notifiable))
            ->line('Jika Anda tidak membuat akun di Tempus Auctions, abaikan email ini.');
    }
}
