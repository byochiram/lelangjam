<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auction_lots', function (Blueprint $table) {
            if (Schema::hasColumn('auction_lots', 'title')) {
                $table->dropColumn('title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('auction_lots', function (Blueprint $table) {
            // fallback kalau someday mau rollback
            if (! Schema::hasColumn('auction_lots', 'title')) {
                $table->string('title')->nullable();
            }
        });
    }
};
