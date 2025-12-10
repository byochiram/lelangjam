<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentPaidNotification extends Notification
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
        $brand = $prod?->brand;
        $model = $prod?->model;
        $title = trim(($brand ?? '') . ' ' . ($model ?? '')) ?: 'Lot #' . $lot?->id;

        $invoiceNo   = $this->payment->invoice_no;
        $amountDue   = (int) $this->payment->amount_due;
        $serviceFee  = (int) ($this->payment->service_fee  ?? 500);
        $shippingFee = (int) ($this->payment->shipping_fee ?? 0);
        $grandTotal  = (int) $this->payment->grand_total;

        // mau ke detail 1 transaksi
        $url = route('transactions.show', $this->payment);

        return (new MailMessage)
            ->subject('Tempus Auctions — Pembayaran Anda telah diterima')
            ->greeting('Halo ' . ($notifiable->name ?: '') . ',')
            ->line('Terima kasih, kami telah menerima pembayaran Anda untuk lelang berikut:')
            ->line("**{$title}** (Lot #{$lot->id})")
            ->line(' ')
            ->line('**Rincian Pembayaran**')
            ->line('Nomor Invoice: **' . $invoiceNo . '**')
            ->line('Harga lelang: **Rp ' . number_format($amountDue, 0, ',', '.') . '**')
            ->line('Biaya layanan: **Rp ' . number_format($serviceFee, 0, ',', '.') . '**')
            ->line('Ongkos kirim: **Rp ' . number_format($shippingFee, 0, ',', '.') . '**')
            ->line('Total dibayar: **Rp ' . number_format($grandTotal, 0, ',', '.') . '**')
            ->line(' ')
            ->action('Lihat Detail Transaksi', $url)
            ->line('Barang Anda akan segera kami proses untuk pengiriman.')
            ->line('Terima kasih telah mempercayai Tempus Auctions. Sampai jumpa di lelang berikutnya!');
    }
}
