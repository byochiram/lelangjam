<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AuctionLot;

class ProcessEndedAuctions extends Command
{
    protected $signature = 'auctions:process-ended';

    protected $description = 'Proses lot lelang yang sudah berakhir (tetapkan pemenang & buat invoice).';

    public function handle(): int
    {
        // ambil semua lot yang:
        // - sudah berakhir (scope ended = end_at < now & tidak dibatalkan)
        // - belum ada winner_bid_id
        // - belum punya payment
        $lots = AuctionLot::query()
            ->ended()
            ->whereNull('winner_bid_id')
            ->whereDoesntHave('payment')
            ->with(['product', 'bids.bidderProfile'])
            ->get();

        if ($lots->isEmpty()) {
            $this->info('Tidak ada lot yang perlu diproses.');
            return Command::SUCCESS;
        }

        $this->info("Memproses {$lots->count()} lot yang sudah berakhir...");

        foreach ($lots as $lot) {
            $this->line(" - Lot #{$lot->id} ({$lot->title})");
            $lot->processAfterEnd(); // di dalamnya akan panggil Payment::createForWinner()
        }

        $this->info('Selesai.');

        return Command::SUCCESS;
    }
}
