<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;
use App\Models\AuctionLot;

class Product extends Model
{
    protected $fillable = ['brand','model','category','year', 'condition','description', 'weight_grams'];

    protected static function booted(): void
    {
        static::deleting(function (Product $product) {
            // hapus file fisik SEMUA image produk ini
            foreach ($product->images as $img) {
                Storage::disk('public')->delete('products/'.$img->filename);
            }
            // DB child rows akan terhapus oleh cascadeOnDelete (foreign key)
        });
    }

    public function images()
    {
        return $this->hasMany(Image::class)->orderBy('sort_order');
    }

    public function auctionLots()
    {
        return $this->hasMany(AuctionLot::class, 'product_id');
    }
}
