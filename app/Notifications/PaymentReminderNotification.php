<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReminderNotification extends Notification
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

        $amount    = $this->payment->amount_due;
        $invoiceNo = $this->payment->invoice_no;
        $expiresAt = $this->payment->expires_at?->format('d M Y H:i');

        return (new MailMessage)
            ->subject('Tempus Auctions — Pengingat Pembayaran Lelang')
            ->greeting('Halo ' . ($notifiable->name ?: '') . ',')
            ->line('Ini adalah pengingat bahwa pembayaran untuk lelang berikut masih menunggu diselesaikan:')
            ->line("**{$title}** (Lot #{$lot->id})")
            ->line(' ')
            ->line('**Detail Invoice**')
            ->line('Nomor Invoice: **' . $invoiceNo . '**')
            ->line('Nominal yang harus dibayar saat ini (belum termasuk biaya layanan & ongkir jika belum dikonfirmasi): **Rp ' . number_format($amount, 0, ',', '.') . '**')
            ->line('Batas waktu pembayaran: **' . $expiresAt . ' WIB**')
            ->line(' ')
            ->action('Bayar Sekarang', route('transactions.index'))
            ->line('Jika Anda sudah melakukan pembayaran, Anda dapat mengabaikan email ini.');
    }
}
