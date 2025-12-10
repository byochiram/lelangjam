<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bidder_profiles', function (Blueprint $table) {
            // pakai string code (sama seperti city & province)
            $table->string('district')->nullable()->after('city');   // Kecamatan
            $table->string('village')->nullable()->after('district'); // Kelurahan/Desa
        });
    }

    public function down(): void
    {
        Schema::table('bidder_profiles', function (Blueprint $table) {
            $table->dropColumn(['district', 'village']);
        });
    }
};
