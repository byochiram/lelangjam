<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // setelah shipping_status biar ngumpul
            $table->timestamp('shipping_shipped_at')->nullable()->after('shipping_status');
            $table->timestamp('shipping_completed_at')->nullable()->after('shipping_shipped_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['shipping_shipped_at', 'shipping_completed_at']);
        });
    }
};
