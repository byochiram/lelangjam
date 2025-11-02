<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::updateOrCreate(['brand'=>'Rolex','model'=>'Submariner'], [
            'category'=>'Automatic','year'=>1998,'condition'=>'USED','description'=>'Vintage Submariner 16610'
        ]);

        Product::updateOrCreate(['brand'=>'Omega','model'=>'Speedmaster'], [
            'category'=>'Chronograph','year'=>2010,'condition'=>'USED','description'=>'Moonwatch Professional'
        ]);
    }
}
