<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bidder_profile_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('lot_id')
                  ->constrained('auction_lots')
                  ->cascadeOnDelete();
            $table->timestamps();

            // 1 bidder_profile tidak bisa nonton lot yang sama dua kali
            $table->unique(['bidder_profile_id', 'lot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};

