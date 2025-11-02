<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'lot_id','user_id','invoice_no','amount_due','status','issued_at','paid_at','expires_at',
        'channel','payment_instructions','pg_order_id','courier','tracking_number','address','city','postal_code','phone'
    ];
    protected $casts = [
        'amount_due'=>'decimal:2',
        'issued_at'=>'datetime','paid_at'=>'datetime','expires_at'=>'datetime',
        'status'=>PaymentStatus::class,
        'payment_instructions'=>'array',
    ];

    public function lot(){ return $this->belongsTo(AuctionLot::class,'lot_id'); }
    public function user(){ return $this->belongsTo(User::class); }
}
