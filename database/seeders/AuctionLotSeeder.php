<?php

namespace Database\Seeders;

use App\Models\AuctionLot;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuctionLotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prod = Product::first();

        if ($prod) {
            AuctionLot::updateOrCreate(
                ['product_id'=>$prod->id,'title'=>'Lelang '.$prod->brand.' '.$prod->model],
                [
                    'start_price'=>50000000,
                    'increment'=>1000000,
                    'current_price'=>50000000,
                    'start_at'=>now(),
                    'end_at'=>now()->addMinutes(15),
                    'status'=>'ACTIVE',
                ]
            );
        }
    }
}
