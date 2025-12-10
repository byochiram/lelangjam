<?php

namespace App\Http\Controllers\Bidder;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\DuitkuService;
use Illuminate\Support\Facades\Log;

class PaymentCheckoutController extends Controller
{
    /**S
     * Dipanggil dari tombol "Bayar sekarang".
     * Tugasnya:
     * - pastikan invoice milik user
     * - kalau perlu, buat invoice baru ke Duitku
     * - LANGSUNG redirect ke halaman pembayaran Duitku
     */
    public function show(Payment $payment, DuitkuService $duitku)
    {
        $user = auth()->user();

        // pastikan pemilik invoice
        if (! $payment->bidderProfile || $payment->bidderProfile->user_id !== $user?->id) {
            abort(403);
        }

        // kalau sudah bayar → balik ke detail transaksi
        if ($payment->status === 'PAID') {
            return redirect()
                ->route('transactions.show', $payment)
                ->with('status', 'Invoice ini sudah dibayar.');
        }

        // kalau sudah expired → jangan buat invoice baru
        if ($payment->status === 'EXPIRED') {
            return redirect()
                ->route('transactions.show', $payment)
                ->with('status', 'Invoice ini sudah kedaluwarsa.');
        }

        // pastikan sudah punya reference & payment_url
        $instructions = $payment->payment_instructions ?? [];

        if (empty($instructions['reference']) || empty($instructions['payment_url'])) {
            $duitku->createInvoice($payment);
            $payment->refresh();
            $instructions = $payment->payment_instructions ?? [];
        }

        $paymentUrl = $instructions['payment_url'] ?? null;

        if (! $paymentUrl) {
            return redirect()
                ->route('transactions.show', $payment)
                ->with('status', 'Gagal membuat link pembayaran. Silakan coba lagi.');
        }

        // 🔥 INI BAGIAN PENTING:
        // Langsung lempar user ke halaman pembayaran Duitku
        return redirect()->away($paymentUrl);
    }

    /**
     * Return URL setelah user menutup halaman pembayaran.
     * Di docs, ini optional. Kita cukup arahkan balik ke detail transaksi.
     */
    public function return(Payment $payment)
    {
        return redirect()->route('transactions.show', $payment);
    }

    /**
     * Callback / Notifikasi dari Duitku
     * Dipanggil dari server Duitku (tanpa auth & tanpa CSRF).
     */
    public function callback(DuitkuService $duitku)
    {
        Log::info('Duitku callback HIT', request()->all());

        $notif = $duitku->handleCallback();

        Log::info('Duitku callback parsed', $notif);

        $merchantOrderId = $notif['merchantOrderId'] ?? null;
        $resultCode      = $notif['resultCode']      ?? null;
        $reference       = $notif['reference']       ?? null; // biasanya ikut dikirim

        if (! $merchantOrderId) {
            Log::warning('Duitku callback without merchantOrderId', $notif);
            return response()->json(['message' => 'Invalid callback'], 400);
        }

        $payment = Payment::where('invoice_no', $merchantOrderId)->first();

        if (! $payment) {
            Log::warning('Duitku callback payment not found', ['merchantOrderId' => $merchantOrderId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // optional: kalau sudah PAID sebelumnya, jangan diubah lagi
        if ($payment->status === 'PAID') {
            Log::info('Duitku callback: payment already PAID', ['invoice' => $payment->invoice_no]);
            return response()->json(['message' => 'OK (already paid)']);
        }

        $update = [];

        // simpan pg_transaction_id dari reference (sekali saja)
        if ($reference && ! $payment->pg_transaction_id) {
            $update['pg_transaction_id'] = $reference;
        }

        if ($resultCode === '00') {
            // pembayaran sukses
            $update['status']  = 'PAID';
            $update['paid_at'] = now();

            // 🔹 integrasi step 5:
            // kalau status pengiriman belum di-set, anggap "Sedang dikemas"
            if (! $payment->shipping_status || $payment->shipping_status === 'PENDING') {
                $update['shipping_status'] = 'PACKING'; // alias sedang dikemas
            }
        } elseif ($resultCode === '01') {
            // dibatalkan
            $update['status'] = 'CANCELLED';
        } else {
            // kadaluarsa / gagal
            $update['status'] = 'EXPIRED';
        }

        $payment->update($update);

        return response()->json(['message' => 'OK']);
    }

}
