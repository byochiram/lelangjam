<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Hapus kolom yang sudah tidak dipakai
            if (Schema::hasColumn('payments', 'courier')) {
                $table->dropColumn('courier');
            }

            if (Schema::hasColumn('payments', 'tracking_number')) {
                $table->dropColumn('tracking_number');
            }

            if (Schema::hasColumn('payments', 'village')) {
                $table->dropColumn('village');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Tambahkan kembali kalau di-rollback
            if (! Schema::hasColumn('payments', 'courier')) {
                $table->string('courier')->nullable();
            }

            if (! Schema::hasColumn('payments', 'tracking_number')) {
                $table->string('tracking_number')->nullable();
            }

            if (! Schema::hasColumn('payments', 'village')) {
                $table->string('village')->nullable();
            }
        });
    }
};
