<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable)
    {
        // ambil durasi expire dari config (biasanya 60 menit)
        $expiry = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');

        return (new MailMessage)
            ->subject('Tempus Auctions - Reset Kata Sandi')
            ->greeting('Halo ' . ($notifiable->name ?: ''))
            ->line('Kami menerima permintaan untuk reset kata sandi akun Tempus Auctions Anda.')
            ->line('Klik tombol berikut untuk mengatur ulang kata sandi.')
            ->action('Reset Kata Sandi', $this->resetUrl($notifiable))
            ->line("Link reset ini hanya berlaku selama {$expiry} menit.")
            ->line('Jika Anda tidak merasa meminta reset kata sandi, Anda dapat mengabaikan email ini.');
    }

    /**
     * Url reset password (Laravel 10+ pakai route bawaan).
     */
    protected function resetUrl($notifiable)
    {
        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
