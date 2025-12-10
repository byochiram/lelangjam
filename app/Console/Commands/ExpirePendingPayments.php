<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Notifications\AccountSuspendedNotification;

class ExpirePendingPayments extends Command
{
    protected $signature   = 'payments:expire-pending';
    protected $description = 'Set payment PENDING yang lewat jatuh tempo menjadi EXPIRED dan suspend bidder 7 hari';

    public function handle(): int
    {
        $now = now();

        // Ambil semua payment yang sudah lewat expires_at tapi belum dibayar
        $payments = Payment::pendingExpired()
            ->with(['bidderProfile.user', 'lot'])
            ->get();

        if ($payments->isEmpty()) {
            $this->info('Tidak ada payment PENDING yang kadaluarsa.');
            return Command::SUCCESS;
        }

        $this->info("Memproses {$payments->count()} payment yang kadaluarsa...");

        DB::transaction(function () use ($payments, $now) {
            foreach ($payments as $payment) {
                // 1. Ubah status payment
                $payment->status = 'EXPIRED';
                $payment->save();

                // 2. Suspend user 7 hari
                $user = $payment->user; // dari accessor getUserAttribute()

                if ($user) {
                    // hanya kalau belum suspended permanen misalnya
                    if ($user->status !== 'SUSPENDED') {
                        $user->status = 'SUSPENDED';
                    }

                    // catat sampai kapan suspend
                    $user->suspended_until = $now->copy()->addDays(7);

                    $invoice = $payment->invoice_no ?? $payment->id;
                    $user->suspend_reason = "Suspend otomatis 7 hari karena tidak menyelesaikan pembayaran invoice #{$invoice} sebelum jatuh tempo.";

                    $user->save();

                    // kirim email notifikasi suspend
                    $user->notify(new AccountSuspendedNotification(
                        until: $user->suspended_until,
                        reason: $user->suspend_reason,
                    ));

                    $this->line(" - Payment #{$payment->id} EXPIRED, email notif suspend dikirim.");
                    $this->line(
                        "   user #{$user->id} ({$user->username}) disuspend sampai "
                        . $user->suspended_until->format('d-m-Y H:i')
                    );
                } else {
                    $this->line(" - Payment #{$payment->id} EXPIRED (user tidak ditemukan).");
                }

                // 3. Status lot akan otomatis tampil UNSOLD
                //    karena accessor runtime_status di AuctionLot
                //    sudah mapping: EXPIRED/CANCELLED => UNSOLD.
            }
        });

        $this->info('Selesai memproses payment kadaluarsa.');

        return Command::SUCCESS;
    }
}
