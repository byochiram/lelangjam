<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = ['lot_id', 'bidder_profile_id', 'amount'];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function lot()
    {
        return $this->belongsTo(AuctionLot::class, 'lot_id');
    }

    public function bidderProfile()
    {
        return $this->belongsTo(BidderProfile::class);
    }
}
