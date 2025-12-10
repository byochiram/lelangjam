<?php

namespace App\Services;

use App\Models\Payment;
use Duitku\Config as DuitkuConfig;
use Duitku\Pop;

class DuitkuService
{
    protected DuitkuConfig $config;

    public function __construct()
    {
        $this->config = new DuitkuConfig(
            config('duitku.merchant_key'),
            config('duitku.merchant_code'),
        );

        // true = sandbox, false = production (kebalikan dari docs, hati-hati)
        $this->config->setSandboxMode((bool) config('duitku.is_sandbox'));

        // optional
        $this->config->setSanitizedMode(false);
        $this->config->setDuitkuLogs(false);
    }

    /**
     * Buat invoice ke Duitku POP untuk satu Payment.
     * Update kolom Payment & return data penting.
     */
    public function createInvoice(Payment $payment): array
    {
        $user = $payment->user;

        // --- Hitung komponen biaya ---
        $baseAmount  = (int) round($payment->amount_due);      // harga lelang
        $shippingFee = (int) ($payment->shipping_fee ?? 0);    // dari RajaOngkir (bisa 0)
        $serviceFee  = 500;                                    // biaya layanan tetap

        // TOTAL yang dikirim ke Duitku
        $amount      = $baseAmount + $shippingFee + $serviceFee;

        $merchantOrderId = $payment->invoice_no;

        $callbackUrl = config('duitku.callback_url') ?: route('duitku.callback');
        $returnUrl   = config('duitku.return_url')   ?: route('checkout.return', $payment);

        $email       = $user?->email ?? 'customer@example.com';
        $phoneNumber = $payment->phone ?: $payment->bidderProfile?->phone ?: '08123456789';

        $productDetails = 'Pembayaran Lelang Lot-'.$payment->lot_id;

        $firstName  = $user?->name ?? 'Bidder';
        $lastName   = ''; // kalau mau di-split boleh

        // --- Alamat untuk billing & shipping ---
        $address = [
            'firstName'   => $firstName,
            'lastName'    => $lastName,
            'address'     => $payment->address ?: '-',
            'city'        => $payment->city_name ?: 'Kota',
            'postalCode'  => $payment->postal_code ?: '00000',
            'phone'       => $phoneNumber,
            'countryCode' => 'ID',
        ];

        // if ($payment->province) {
        //     $address['address'] = trim(($payment->address ?: '-') . ' - ' . $payment->province_name);
        // }

        $addressLine = $payment->address ?: '-';

        if ($payment->village_name) {
            $addressLine .= ', ' . $payment->village_name;
        }
        if ($payment->district_name) {
            $addressLine .= ', Kec. ' . $payment->district_name;
        }
        if ($payment->province_name) {
            $addressLine .= ' - ' . $payment->province_name;
        }

        $address['address'] = trim($addressLine);

        $customerDetail = [
            'firstName'       => $firstName,
            'lastName'        => $lastName,
            'email'           => $email,
            'phoneNumber'     => $phoneNumber,
            'billingAddress'  => $address,
            'shippingAddress' => $address,
        ];

        // --- Breakdown itemDetails (supaya di Duitku kelihatan rinci) ---
        $itemDetails = [];

        // 1. Harga lelang
        $itemDetails[] = [
            'name'     => 'Lelang Lot-'.$payment->lot_id,
            'price'    => $baseAmount,
            'quantity' => 1,
        ];

        // 2. Ongkos kirim (kalau ada)
        if ($shippingFee > 0) {
            $itemDetails[] = [
                'name'     => 'Ongkos Kirim',
                'price'    => $shippingFee,
                'quantity' => 1,
            ];
        }

        // 3. Biaya layanan
        $itemDetails[] = [
            'name'     => 'Biaya Layanan',
            'price'    => $serviceFee,
            'quantity' => 1,
        ];

        $expiryMinutes = 10;

        $params = [
            'paymentAmount'   => $amount,          // TOTAL (lelang + ongkir + layanan)
            'merchantOrderId' => $merchantOrderId,
            'productDetails'  => $productDetails,  // deskripsi umum
            'additionalParam' => '',
            'merchantUserInfo'=> '',
            'customerVaName'  => $firstName,
            'email'           => $email,
            'phoneNumber'     => $phoneNumber,
            'itemDetails'     => $itemDetails,     // breakdown
            'customerDetail'  => $customerDetail,
            'callbackUrl'     => $callbackUrl,
            'returnUrl'       => $returnUrl,
            'expiryPeriod'    => $expiryMinutes,   // menit
        ];

        // --- Call ke Duitku POP ---
        $responseJson = Pop::createInvoice($params, $this->config);
        $data         = json_decode($responseJson, true);

        \Log::info('Duitku createInvoice response', $data ?? []);

        // Ambil reference untuk disimpan sebagai pg_transaction_id
        $pgTxId = $data['reference'] ?? null;

        // Biasanya ada 'reference' & 'paymentUrl'
        $payment->update([
            //'channel'              => 'DUITKU_POP',
            'payment_instructions' => [
                'reference'    => $data['reference']  ?? null,
                'payment_url'  => $data['paymentUrl'] ?? null,
                'va_number'    => $data['vaNumber']   ?? null,
                'expiryPeriod' => $expiryMinutes,
            ],
            'pg_transaction_id' => $pgTxId,
            // kalau expires_at belum ada, set 10 menit dari sekarang
            'expires_at'        => $payment->expires_at ?? now()->addMinutes($expiryMinutes),
        ]);

        return $data;
    }

    /**
     * Helper untuk callback notification
     */
    public function handleCallback(): array
    {
        $callbackJson = Pop::callback($this->config);
        $data         = json_decode($callbackJson, true);

        \Log::info('Duitku callback received', $data ?? []);

        return $data ?? [];
    }
}
