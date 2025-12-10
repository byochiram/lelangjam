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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')->unique()->constrained('auction_lots')->cascadeOnDelete();
            $table->foreignId('bidder_profile_id')->constrained('bidder_profiles');
            $table->string('invoice_no')->unique();
            $table->decimal('amount_due',14,2);
            $table->string('status')->default('PENDING')->index();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->string('channel')->nullable();                  // VA/QR/card
            $table->json('payment_instructions')->nullable();       // detail VA/QR
            $table->string('pg_order_id')->nullable();              // id dari gateway
            $table->string('courier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
