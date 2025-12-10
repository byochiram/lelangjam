<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->constrained('auction_lots')->cascadeOnDelete();
            $table->foreignId('bidder_profile_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount',14,2);
            $table->timestamps();
            $table->index(['lot_id','created_at']);
            $table->index(['lot_id','amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
