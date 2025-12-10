<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Notifications\PaymentPaidNotification;

class Payment extends Model
{
    protected $fillable = [
        'lot_id',
        'bidder_profile_id',
        'invoice_no',
        'amount_due',
        'status',
        'issued_at',
        'paid_at',
        'expires_at',
        'payment_instructions',
        'pg_transaction_id',

        'address',
        'city',
        'district',
        'province', 
        'postal_code',
        'phone',

        'shipping_rajaongkir_district_id',
        'shipping_weight',
        'shipping_courier',
        'shipping_service',
        'shipping_fee',
        'shipping_etd',
        'shipping_tracking_no',
        'shipping_status',
        'shipping_raw_response',
        'shipping_shipped_at',
        'shipping_completed_at',
    ];

    protected $casts = [
        'amount_due'           => 'decimal:2',
        'issued_at'            => 'datetime',
        'paid_at'              => 'datetime',
        'expires_at'           => 'datetime',
        'payment_instructions' => 'array',
        'shipping_raw_response'=> 'array',
        'shipping_shipped_at'   => 'datetime',
        'shipping_completed_at' => 'datetime',
    ];

    /* -------------------------------------------------
     |  Relasi
     * ------------------------------------------------- */

    public function lot()
    {
        return $this->belongsTo(AuctionLot::class, 'lot_id');
    }

    public function bidderProfile()
    {
        return $this->belongsTo(BidderProfile::class, 'bidder_profile_id');
    }

    public function getUserAttribute()
    {
        return $this->bidderProfile?->user;
    }

    /* -------------------------------------------------
     |  Factory untuk winner lot
     * ------------------------------------------------- */

    /**
     * Buat Payment baru untuk pemenang lot yang sudah berakhir.
     * - Salin alamat dari payment terakhir bidder (kalau ada).
     * - Set shipping_weight dari product.weight_grams (sudah termasuk packing).
     */
    public static function createForWinner(AuctionLot $lot): ?self
    {
        $winnerBid = $lot->winnerBid;
        $profile   = $winnerBid?->bidderProfile;
        $amount    = $winnerBid?->amount;

        if (! $profile || ! $amount) {
            return null;
        }

        $invoiceNo = 'INV-'.now()->format('Ymd').'-'.$lot->id.'-'.Str::upper(Str::random(4));

        // --- 1) Basis alamat: kosong dulu ---
        $addressData = [
            'address'                         => null,
            'city'                            => null,
            'district'                        => null,
            'village'                         => null,
            'province'                        => null,
            'postal_code'                     => null,
            'phone'                           => $profile->phone,
            'shipping_rajaongkir_district_id' => null,
        ];

        // --- 2) Kalau bidder ini sudah pernah punya payment, salin alamat terakhir ---
        $lastPayment = self::where('bidder_profile_id', $profile->id)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->first();

        if ($lastPayment) {
            $addressData = [
                'address'                         => $lastPayment->address,
                'city'                            => $lastPayment->city,
                'district'                        => $lastPayment->district,
                'village'                         => $lastPayment->village,
                'province'                        => $lastPayment->province,
                'postal_code'                     => $lastPayment->postal_code,
                'phone'                           => $lastPayment->phone ?? $profile->phone,
                'shipping_rajaongkir_district_id' => $lastPayment->shipping_rajaongkir_district_id,
            ];
        }

        // --- 3) Hitung default shipping_weight dari product.weight_grams ---
        $productWeight = (int) ($lot->product?->weight_grams ?? 0);
        $shippingWeight = $productWeight > 0
            ? $productWeight
            : (int) config('rajaongkir.default_weight', 1000);

        $addressData['shipping_weight'] = $shippingWeight;

        // --- 4) Buat payment ---
        return self::create(array_merge([
            'lot_id'            => $lot->id,
            'bidder_profile_id' => $profile->id,
            'invoice_no'        => $invoiceNo,
            'amount_due'        => $amount,
            'status'            => 'PENDING',
            'issued_at'         => now(),
            // kebijakan sekarang masih 10 menit (sinkron sama DuitkuService)
            'expires_at'        => now()->addMinutes(10),
        ], $addressData));
    }

     /* -------------------------------------------------
     |  Accessor "nama kota/provinsi" (pakai kolom Payment sendiri)
     * ------------------------------------------------- */

    public function getCityNameAttribute(): ?string
    {
        // sekarang city disimpan langsung sebagai nama kota
        return $this->city ?: null;
    }

    public function getProvinceNameAttribute(): ?string
    {
        return $this->province ?: null;
    }

    public function getDistrictNameAttribute(): ?string
    {
        return $this->district ?: null;
    }

    public function getGrandTotalAttribute()
    {
        // sesuaikan kalau fee kamu simpan di kolom lain
        $serviceFee  = $this->service_fee ?? 500;
        $shippingFee = $this->shipping_fee ?? 0;

        return (int) $this->amount_due + (int) $serviceFee + (int) $shippingFee;
    }

    /* -------------------------------------------------
     |  Scope helper
     * ------------------------------------------------- */

    public function scopePendingExpired($query)
    {
        return $query->where('status', 'PENDING')
            ->whereNull('paid_at')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    protected static function booted()
    {
        static::updated(function (Payment $payment) {
            // Pastikan status baru = PAID dan status lama BUKAN PAID
            if ($payment->wasChanged('status') && $payment->status === 'PAID') {

                $profile = $payment->bidderProfile;

                if ($profile) {
                    // pakai grand_total: harga lelang + ongkir + service_fee
                    $amount = $payment->grand_total;

                    // Tambah total_spent
                    $profile->increment('total_spent', $amount);

                    // 🔔 Kirim notifikasi email "Pembayaran berhasil"
                    $user = $profile->user;
                    if ($user) {
                        $user->notify(new PaymentPaidNotification($payment));
                    }
                }
            }
        });
    }   

}
