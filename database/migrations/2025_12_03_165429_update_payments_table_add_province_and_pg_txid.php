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
        Schema::table('payments', function (Blueprint $table) {
            // 1) tambah province
            $table->string('province')->nullable()->after('city');

            // 2) hapus channel (kalau sudah ada datanya dan mau disimpan, backup dulu)
            $table->dropColumn('channel');

            // 3) rename pg_order_id -> pg_transaction_id
            $table->renameColumn('pg_order_id', 'pg_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // rollback
            $table->string('channel')->nullable()->after('paid_at');
            $table->renameColumn('pg_transaction_id', 'pg_order_id');
            $table->dropColumn('province');
        });
    }
};
