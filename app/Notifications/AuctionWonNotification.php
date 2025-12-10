<?php

namespace App\Notifications;

use App\Models\AuctionLot;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionWonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AuctionLot $lot,
        public Payment $payment
    ) {}

    public function via($notifiable)
    {
        // Nanti bisa tambah 'database' kalau mau simpan ke tabel notifications
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $product   = $this->lot->product;
        $brand     = $product?->brand;
        $model     = $product?->model;
        $title     = trim($brand.' '.$model) ?: 'Lot #'.$this->lot->id;

        $amount    = $this->payment->amount_due;
        $invoiceNo = $this->payment->invoice_no;
        $expiresAt = $this->payment->expires_at?->format('d M Y H:i');

        // Link ke halaman transaksi (list), atau sesuaikan kalau punya detail view:
        $url = route('transactions.index');

        return (new MailMessage)
            ->subject('Tempus Auctions — Selamat! Anda memenangkan lelang')
            ->greeting('Halo ' . ($notifiable->name ?: '') . ',')
            ->line("Selamat! Anda berhasil memenangkan lelang berikut di Tempus Auctions:")
            ->line("**{$title}** (Lot #{$this->lot->id})")
            ->line(' ')
            ->line('**Detail Pembayaran**')
            ->line('Nomor Invoice: **' . $invoiceNo . '**')
            ->line('Harga lelang (belum termasuk biaya layanan & ongkir): **Rp ' . number_format($amount, 0, ',', '.') . '**')
            ->line('Biaya layanan dan ongkos kirim akan dihitung saat Anda melengkapi alamat dan memilih kurir pada halaman pembayaran.')
            ->line('Batas waktu pembayaran: **' . $expiresAt . ' WIB**')
            ->line(' ')
            ->action('Selesaikan Pembayaran', $url)
            ->line('Segera lakukan pembayaran sebelum waktu jatuh tempo. Jika pembayaran tidak dilakukan, sistem secara otomatis akan menangguhkan akun Anda.')
            ->line('Apabila Anda merasa tidak melakukan bid pada lot ini, silakan hubungi tim Tempus Auctions.');
    }
}
