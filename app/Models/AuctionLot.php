<?php

namespace App\Models;

use App\Enums\LotStatus;
use Illuminate\Database\Eloquent\Model;

class AuctionLot extends Model
{
    protected $fillable = [
        'product_id','title','start_price','increment','current_price',
        'start_at','end_at','status','winner_bid_id','winner_user_id'
    ];

    protected $casts = [
        'start_price'=>'decimal:2',
        'increment'=>'decimal:2',
        'current_price'=>'decimal:2',
        'start_at'=>'datetime',
        'end_at'=>'datetime',
        'status'=>LotStatus::class,
    ];

    public function product(){ return $this->belongsTo(Product::class); }
    public function bids(){ return $this->hasMany(Bid::class,'lot_id'); }
    public function payment(){ return $this->hasOne(Payment::class,'lot_id'); }
    public function winnerBid(){ return $this->belongsTo(Bid::class,'winner_bid_id'); }
    public function winner(){ return $this->belongsTo(User::class,'winner_user_id'); }
}
