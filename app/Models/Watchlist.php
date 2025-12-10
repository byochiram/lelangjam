<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    protected $fillable = ['bidder_profile_id', 'lot_id'];

    public function bidderProfile()
    {
        return $this->belongsTo(BidderProfile::class);
    }

    public function lot()
    {
        return $this->belongsTo(AuctionLot::class, 'lot_id');
    }
}

