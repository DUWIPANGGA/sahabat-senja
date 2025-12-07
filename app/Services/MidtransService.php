<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Donasi;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createTransaction(Donasi $donasi, array $customerDetails)
    {
        $transactionDetails = [
            'order_id' => $donasi->kode_donasi,
            'gross_amount' => $donasi->jumlah,
        ];

        $itemDetails = [
            [
                'id' => 'donasi-' . $donasi->kampanye_donasi_id,
                'price' => $donasi->jumlah,
                'quantity' => 1,
                'name' => 'Donasi untuk ' . ($donasi->kampanye->judul ?? 'Kampanye'),
                'category' => 'Donasi'
            ]
        ];

        $params = [
            'transaction_details' => $transactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails,
            'callbacks' => [
                'finish' => config('app.url') . '/api/donasi/callback/finish',
                'error' => config('app.url') . '/api/donasi/callback/error',
                'pending' => config('app.url') . '/api/donasi/callback/pending',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Snap token: ' . $e->getMessage());
        }
    }

    public function getSnapJsUrl()
    {
        return config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }
}