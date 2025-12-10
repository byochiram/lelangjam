<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $lot   = $this->payment->lot;
        $prod  = $lot?->product;
        $title = trim(($prod->brand ?? '') . ' ' . ($prod->model ?? '')) ?: 'Lot #' . $lot->id;

        return (new MailMessage)
            ->subject('Tempus Auctions — Barang Telah Anda Terima')
            ->greeting('Halo ' . ($notifiable->name ?: '') . ',')
            ->line('Terima kasih telah mengonfirmasi bahwa barang lelang berikut telah Anda terima:')
            ->line("**{$title}** (Lot #{$lot->id})")
            ->line(' ')
            ->line('Kami harap pengalaman berbelanja dan mengikuti lelang di Tempus Auctions memberikan kesan yang memuaskan.')
            ->action('Lihat Riwayat Transaksi', route('transactions.index'))
            ->line('Sampai jumpa di lelang berikutnya, dan semoga menemukan koleksi terbaik untuk Anda!');
    }
}
