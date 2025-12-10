<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // berat dalam gram, optional
            $table->unsignedInteger('weight_grams')
                  ->nullable()
                  ->after('year'); // sejajar dengan tahun & kondisi
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('weight_grams');
        });
    }
};
