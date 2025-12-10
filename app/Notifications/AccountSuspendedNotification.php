<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AccountSuspendedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ?\Carbon\Carbon $until = null,
        public ?string $reason = null,
    ) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Tempus Auctions - Akun Anda sedang ditangguhkan')
            ->greeting('Halo ' . ($notifiable->name ?: ''))
            ->line('Kami informasikan bahwa akun Anda saat ini sedang ditangguhkan dan tidak dapat digunakan untuk mengikuti lelang baru.');

        if ($this->until) {
            $message->line('Penangguhan berlaku sampai: ' . $this->until->format('d M Y H:i'));
        }

        if ($this->reason) {
            $message->line('Alasan penangguhan: ' . $this->reason);
        }

        return $message
            ->line('Anda masih dapat masuk untuk melihat riwayat lelang dan transaksi.')
            ->line('Jika Anda memerlukan klarifikasi, silakan hubungi tim Tempus Auctions.');
    }
}
