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
        Schema::create('auction_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->string('title');
            $table->decimal('start_price',14,2);
            $table->decimal('increment',14,2)->default(1);
            $table->decimal('current_price',14,2);
            $table->timestamp('start_at')->useCurrent();
            $table->timestamp('end_at')->useCurrent();
            $table->string('status')->default('SCHEDULED')->index();
            //$table->foreignId('winner_bid_id')->nullable()->constrained('bids')->nullOnDelete();
            $table->unsignedBigInteger('winner_bid_id')->nullable();
            $table->foreignId('winner_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['start_at','end_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_lots');
    }
};
