<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Notifications\PaymentReminderNotification;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature   = 'payments:send-reminders';
    protected $description = 'Kirim email pengingat untuk payment PENDING yang akan segera jatuh tempo';

    public function handle(): int
    {
        $now = now();

        // Misal: kirim reminder untuk payment yang akan jatuh tempo
        // dalam 3 menit ke depan (window kecil supaya tidak dobel-dobel).
        $from = $now;
        $to   = $now->copy()->addMinutes(3);

        $payments = Payment::query()
            ->where('status', 'PENDING')
            ->whereNull('paid_at')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$from, $to])
            ->with(['bidderProfile.user', 'lot.product'])
            ->get();

        if ($payments->isEmpty()) {
            $this->info('Tidak ada payment PENDING yang perlu diingatkan.');
            return Command::SUCCESS;
        }

        $this->info("Mengirim pengingat untuk {$payments->count()} payment...");

        foreach ($payments as $payment) {
            $user = $payment->user; // accessor di model Payment

            if (! $user) {
                $this->line(" - Payment #{$payment->id}: user tidak ditemukan, skip.");
                continue;
            }

            // (opsional) kalau user sudah SUSPENDED, boleh saja di-skip
            if ($user->status === 'SUSPENDED') {
                $this->line(" - Payment #{$payment->id}: user sudah SUSPENDED, skip.");
                continue;
            }

            $user->notify(new PaymentReminderNotification($payment));

            $this->line(
                " - Reminder dikirim untuk Payment #{$payment->id} (Invoice {$payment->invoice_no}) ke user #{$user->id}"
            );
        }

        $this->info('Selesai mengirim pengingat pembayaran.');

        return Command::SUCCESS;
    }
}
