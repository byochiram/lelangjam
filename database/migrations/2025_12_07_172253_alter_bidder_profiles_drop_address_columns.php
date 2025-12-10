<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bidder_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'city',
                'district',
                'village',
                'province',
                'postal_code',
                'verified_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('bidder_profiles', function (Blueprint $table) {
            // kembalikan lagi kolom-kolomnya jika di-rollback
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('village')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->timestamp('verified_at')->nullable();
        });
    }
};
