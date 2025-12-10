<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // letaknya setelah city, supaya urutan alamat masih logis
            $table->string('district')->nullable()->after('city');
            $table->string('village')->nullable()->after('district');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['district', 'village']);
        });
    }
};
