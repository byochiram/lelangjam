<?php

namespace App\Models;

use App\Enums\KycStatus;
use Illuminate\Database\Eloquent\Model;

class BidderProfile extends Model
{
    protected $fillable = ['user_id','phone','address','kyc_status','verified_at','bid_count','win_count','total_spent','last_bid_at'];
    protected $casts = [
        'kyc_status'=>KycStatus::class,
        'verified_at'=>'datetime',
        'total_spent'=>'decimal:2',
        'last_bid_at'=>'datetime',
    ];

    public function user(){ return $this->belongsTo(User::class); }
    public function bids(){ return $this->hasMany(Bid::class); }
    public function kycVerifications(){ return $this->hasMany(KycVerification::class); }
}
