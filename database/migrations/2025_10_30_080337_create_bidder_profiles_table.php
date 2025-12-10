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
        Schema::create('bidder_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('phone')->nullable();
            
            // Alamat Lengkap
            $table->text('address')->nullable();      // Alamat detail (jalan, no rumah, dll)
            $table->string('city')->nullable();       // Kota / Kabupaten
            $table->string('province')->nullable();   // Provinsi
            $table->string('postal_code', 10)->nullable(); // Kode pos

            // Verifikasi Identitas (tanpa dokumen)
            $table->timestamp('verified_at')->nullable();

            // Statistik Bidder
            $table->unsignedInteger('bid_count')->default(0);
            $table->unsignedInteger('win_count')->default(0);
            $table->decimal('total_spent', 14, 2)->default(0);
            $table->timestamp('last_bid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidder_profiles');
    }
};
