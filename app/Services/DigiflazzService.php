<?php

namespace App\Services;

use App\Models\Configuration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiflazzService
{
    private $username;
    private $productionKey;
    private $webhookId;
    private $secret;
    private $baseUrl = 'https://api.digiflazz.com/v1';

    public function __construct()
    {
        $this->username = Configuration::getValue('digiflazz_username', '');
        $this->productionKey = Configuration::getValue('digiflazz_production_key', '');
        $this->webhookId = Configuration::getValue('digiflazz_webhook_id', '');
        $this->secret = Configuration::getValue('digiflazz_secret', '');
    }

    /**
     * Generate signature untuk request
     */
    private function generateSignature($data)
    {
        return md5($this->username . $this->productionKey . $data);
    }

    /**
     * Generate signature untuk cek saldo
     */
    private function generateBalanceSignature()
    {
        return md5($this->username . $this->productionKey . 'depo');
    }

    /**
     * Generate signature untuk price list
     */
    private function generatePriceSignature()
    {
        return md5($this->username . $this->productionKey . 'pricelist');
    }

    /**
     * Generate signature untuk top up
     */
    private function generateTopUpSignature($refId)
    {
        return md5($this->username . $this->productionKey . $refId);
    }

    /**
     * Generate signature untuk cek status
     */
    private function generateStatusSignature($refId)
    {
        return md5($this->username . $this->productionKey . $refId);
    }

    /**
     * Cek saldo akun
     */
    public function checkBalance()
    {
        try {
            $signature = $this->generateBalanceSignature();
            
            $payload = [
                'cmd' => 'deposit',
                'username' => $this->username,
                'sign' => $signature
            ];
            
            Log::info('Digiflazz balance check request', [
                'payload' => $payload,
                'signature_input' => $this->username . $this->productionKey . 'depo'
            ]);
            
            $response = Http::post($this->baseUrl . '/cek-saldo', $payload);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Digiflazz balance check response', $data);
                return $data;
            }

            Log::error('Digiflazz balance check failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz balance check exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Cek harga produk
     */
    public function checkPrice($productCode = null)
    {
        try {
            $signature = $this->generatePriceSignature();
            
            $payload = [
                'cmd' => 'prepaid',
                'username' => $this->username,
                'sign' => $signature
            ];

            if ($productCode) {
                $payload['code'] = $productCode;
            }

            $response = Http::post($this->baseUrl . '/price-list', $payload);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Digiflazz price check response', $data);
                return $data;
            }

            Log::error('Digiflazz price check failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz price check exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Top up produk
     */
    public function topUp($buyerSkuCode, $customerNo, $refId = null)
    {
        try {
            $refId = $refId ?? 'REF' . time();
            $signature = $this->generateTopUpSignature($refId);
            
            $payload = [
                'username' => $this->username,
                'buyer_sku_code' => $buyerSkuCode,
                'customer_no' => $customerNo,
                'ref_id' => $refId,
                'sign' => $signature
            ];

            $response = Http::post($this->baseUrl . '/transaction', $payload);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Digiflazz top up response', $data);
                return $data;
            }

            Log::error('Digiflazz top up failed', [
                'status' => $response->status(),
                'response' => $response->body(),
                'payload' => $payload
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz top up exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Cek status transaksi
     */
    public function checkStatus($refId)
    {
        try {
            $signature = $this->generateStatusSignature($refId);
            
            $response = Http::post($this->baseUrl . '/transaction', [
                'cmd' => 'status',
                'username' => $this->username,
                'ref_id' => $refId,
                'sign' => $signature
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Digiflazz status check response', $data);
                return $data;
            }

            Log::error('Digiflazz status check failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz status check exception', [
                'message' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Validasi signature dari callback
     */
    public function validateCallbackSignature($data, $signature)
    {
        $expectedSignature = md5($this->webhookId . $this->secret . $data);
        return $expectedSignature === $signature;
    }

    /**
     * Cek apakah konfigurasi sudah lengkap
     */
    public function isConfigured()
    {
        return !empty($this->username) && 
               !empty($this->productionKey);
    }

    /**
     * Cek apakah webhook sudah dikonfigurasi
     */
    public function isWebhookConfigured()
    {
        return !empty($this->webhookId) && !empty($this->secret);
    }



    /**
     * Test koneksi ke Digiflazz
     */
    public function testConnection()
    {
        try {
            $balance = $this->checkBalance();
            
            if ($balance && isset($balance['data'])) {
                return [
                    'success' => true,
                    'message' => 'Koneksi ke Digiflazz berhasil',
                    'data' => $balance['data']
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke Digiflazz'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
