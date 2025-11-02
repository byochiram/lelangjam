<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycVerification extends Model
{
    protected $fillable = [
        'bidder_profile_id','id_type','nik_hash','nik_last4','full_name','date_of_birth',
        'address','ktp_image_url','selfie_image_url','status','reason','submitted_at','verified_at'
    ];
    protected $casts = ['date_of_birth'=>'date','submitted_at'=>'datetime','verified_at'=>'datetime'];

    public function bidderProfile(){ return $this->belongsTo(BidderProfile::class); }
}
