<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentOnTheWayNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payment $payment,
        public ?string $courier = null,
        public ?string $trackingNumber = null,
    ) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $lot   = $this->payment->lot;
        $prod  = $lot?->product;
        $title = trim(($prod->brand ?? '') . ' ' . ($prod->model ?? '')) ?: 'Lot #' . $lot?->id;

        $courier = $this->courier ?: $this->payment->shipping_courier;
        $resi    = $this->trackingNumber ?: $this->payment->shipping_tracking_no;

        return (new MailMessage)
            ->subject('Tempus Auctions — Barang Lelang Sedang Dikirim')
            ->greeting('Halo ' . ($notifiable->name ?: '') . ',')
            ->line('Kami informasikan bahwa barang lelang Anda sedang dalam proses pengiriman.')
            ->line("**{$title}** (Lot #{$lot->id})")
            ->line(' ')
            ->line('**Detail Pengiriman**')
            ->when($courier, fn ($m) => $m->line('Kurir: **' . $courier . '**'))
            ->when($resi, fn ($m) => $m->line('Nomor resi: **' . $resi . '**'))
            ->when($this->payment->shipping_etd, function ($m) {
                $m->line('Perkiraan waktu tiba: **' . $this->payment->shipping_etd . '**');
            })
            ->line('Alamat tujuan akan dikirim sesuai dengan data yang Anda konfirmasikan pada halaman pembayaran.')
            ->line(' ')
            ->action('Lihat Status Transaksi', route('transactions.index'))
            ->line('Jika dalam beberapa hari ke depan paket belum Anda terima, atau Anda merasa tidak pernah melakukan transaksi ini, segera hubungi tim Tempus Auctions.');
    }
}
