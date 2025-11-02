<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = ['lot_id','bidder_profile_id','amount','placed_at','status'];
    protected $casts = ['amount'=>'decimal:2','placed_at'=>'datetime'];

    public function lot(){ return $this->belongsTo(AuctionLot::class,'lot_id'); }
    public function bidderProfile(){ return $this->belongsTo(BidderProfile::class); }
}
