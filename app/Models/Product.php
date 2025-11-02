<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['brand','model','category','year','condition','description'];

    public function images(){ return $this->hasMany(Image::class); }
    public function lots(){ return $this->hasMany(AuctionLot::class); }
}
