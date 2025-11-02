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
        Schema::table('auction_lots', function (Blueprint $table) {
            $table->foreign('winner_bid_id')
                  ->references('id')->on('bids')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auction_lots', function (Blueprint $table) {
            $table->dropForeign(['winner_bid_id']);
        });
    }
};
