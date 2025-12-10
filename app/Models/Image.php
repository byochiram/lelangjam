<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $fillable = ['product_id','filename','is_primary','sort_order'];
    protected $appends = ['public_url']; // agar bisa dipakai di Blade: $img->public_url
    
    public function getPublicUrlAttribute(): string
    {
        return asset('storage/products/'.$this->filename);
    }

    protected static function booted(): void
    {
        // Hapus file fisik saat image dihapus lewat Eloquent
        static::deleting(function (Image $img) {
            Storage::disk('public')->delete('products/'.$img->filename);
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
