<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {

            $table->unsignedBigInteger('shipping_rajaongkir_district_id')
                ->nullable()
                ->after('pg_transaction_id');

            $table->integer('shipping_weight')
                ->default(0)
                ->after('shipping_rajaongkir_district_id');

            $table->string('shipping_courier')
                ->nullable()
                ->after('shipping_weight');

            $table->string('shipping_service')
                ->nullable()
                ->after('shipping_courier');

            $table->integer('shipping_fee')
                ->default(0)
                ->after('shipping_service');

            $table->string('shipping_etd')
                ->nullable()
                ->after('shipping_fee');

            $table->string('shipping_tracking_no')
                ->nullable()
                ->after('shipping_etd');

            $table->string('shipping_status')
                ->default('PENDING') // PENDING|SHIPPED|DELIVERED
                ->after('shipping_tracking_no');

            $table->json('shipping_raw_response')
                ->nullable()
                ->after('shipping_status');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_rajaongkir_district_id',
                'shipping_weight',
                'shipping_courier',
                'shipping_service',
                'shipping_fee',
                'shipping_etd',
                'shipping_tracking_no',
                'shipping_status',
                'shipping_raw_response',
            ]);
        });
    }
};
