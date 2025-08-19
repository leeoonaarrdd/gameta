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
        // Format: username + api_key + "depo" (sesuai dokumentasi Digiflazz)
        return md5($this->username . $this->productionKey . 'depo');
    }

    /**
     * Generate signature untuk price list
     */
    private function generatePriceSignature()
    {
        // Format: username + api_key + pricelist
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
     * Debug signature generation
     */
    public function debugSignature()
    {
        $username = $this->username;
        $key = $this->productionKey;
        $depo = 'depo'; // Sesuai dokumentasi Digiflazz
        
        $signatureInput = $username . $key . $depo;
        $signature = md5($signatureInput);
        
        return [
            'username' => $username,
            'key_length' => strlen($key),
            'key_preview' => substr($key, 0, 10) . '...',
            'signature_input' => $signatureInput,
            'signature' => $signature,
            'signature_length' => strlen($signature)
        ];
    }

    /**
     * Cek saldo akun
     */
    public function checkBalance()
    {
        try {
            // Validasi konfigurasi
            if (empty($this->username) || empty($this->productionKey)) {
                Log::error('Digiflazz configuration missing', [
                    'username' => $this->username ? 'set' : 'empty',
                    'production_key' => $this->productionKey ? 'set' : 'empty'
                ]);
                return null;
            }

            $signature = $this->generateBalanceSignature();
            $signatureInput = $this->username . $this->productionKey . 'depo'; // Sesuai dokumentasi
            
            $payload = [
                'cmd' => 'deposit',
                'username' => $this->username,
                'sign' => $signature
            ];
            
            Log::info('Digiflazz balance check request', [
                'payload' => $payload,
                'signature_input' => $signatureInput,
                'signature_length' => strlen($signature),
                'username_length' => strlen($this->username),
                'key_length' => strlen($this->productionKey),
                'url' => $this->baseUrl . '/cek-saldo'
            ]);
            
            // Coba endpoint yang berbeda
            $endpoints = [
                $this->baseUrl . '/cek-saldo',
                $this->baseUrl . '/deposit',
                $this->baseUrl . '/balance'
            ];
            
            foreach ($endpoints as $endpoint) {
                Log::info('Trying endpoint: ' . $endpoint);
                
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ])
                    ->post($endpoint, $payload);

                Log::info('Response for ' . $endpoint, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Digiflazz balance check response', $data);
                    return $data;
                }
            }

            Log::error('Digiflazz balance check failed on all endpoints', [
                'endpoints' => $endpoints,
                'payload' => $payload
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Digiflazz balance check exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
    public function topUp($buyerSkuCode, $customerNo, $refId = null, $testing = false, $maxPrice = null, $callbackUrl = null, $allowDot = false)
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

            // Tambahkan parameter opsional sesuai dokumentasi Digiflazz
            if ($testing) {
                $payload['testing'] = true;
            }

            if ($maxPrice !== null) {
                $payload['max_price'] = $maxPrice;
            }

            if ($callbackUrl !== null) {
                $payload['cb_url'] = $callbackUrl;
            }

            if ($allowDot) {
                $payload['allow_dot'] = true;
            }

            Log::info('Digiflazz topup request', [
                'payload' => $payload,
                'testing' => $testing,
                'endpoint' => $this->baseUrl . '/transaction'
            ]);

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
            // Validasi konfigurasi
            if (empty($this->username) || empty($this->productionKey)) {
                return [
                    'success' => false,
                    'message' => 'Konfigurasi Digiflazz belum lengkap. Username dan Production Key harus diisi.',
                    'data' => null
                ];
            }

            // Test dengan cek saldo
            $balance = $this->checkBalance();
            
            if ($balance && isset($balance['data'])) {
                return [
                    'success' => true,
                    'message' => 'Koneksi ke Digiflazz berhasil',
                    'data' => $balance['data']
                ];
            }

            // Jika gagal, coba test dengan price list
            $priceList = $this->checkPrice();
            if ($priceList && isset($priceList['data'])) {
                return [
                    'success' => true,
                    'message' => 'Koneksi ke Digiflazz berhasil (via price list)',
                    'data' => ['connection' => 'ok', 'method' => 'price_list']
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke Digiflazz. Periksa konfigurasi username dan production key.',
                'data' => $balance
            ];
        } catch (\Exception $e) {
            Log::error('Digiflazz test connection exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Test topup dengan produk yang valid
     */
    public function testTopUp($buyerSkuCode, $customerNo, $refId = null)
    {
        try {
            // Validasi input
            if (empty($buyerSkuCode)) {
                return [
                    'success' => false,
                    'message' => 'Buyer SKU Code tidak boleh kosong'
                ];
            }

            if (empty($customerNo)) {
                return [
                    'success' => false,
                    'message' => 'Customer Number tidak boleh kosong'
                ];
            }

            // Cek apakah produk tersedia
            $priceCheck = $this->checkPrice($buyerSkuCode);
            
            if (!$priceCheck || !isset($priceCheck['data'])) {
                return [
                    'success' => false,
                    'message' => 'Produk tidak ditemukan atau tidak tersedia'
                ];
            }

            // Cari produk yang sesuai
            $product = null;
            foreach ($priceCheck['data'] as $item) {
                if ($item['buyer_sku_code'] === $buyerSkuCode) {
                    $product = $item;
                    break;
                }
            }

            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Produk dengan SKU ' . $buyerSkuCode . ' tidak ditemukan'
                ];
            }

            // Log product info untuk debugging
            Log::info('Product found for topup test', [
                'buyer_sku_code' => $buyerSkuCode,
                'product' => $product
            ]);

            // Cek status produk (jika ada)
            if (isset($product['status']) && $product['status'] !== 'active') {
                return [
                    'success' => false,
                    'message' => 'Produk tidak aktif: ' . $product['status']
                ];
            }

            // Lakukan topup dengan testing mode untuk development
            $topUpResult = $this->topUp($buyerSkuCode, $customerNo, $refId, true); // testing = true

            if ($topUpResult && isset($topUpResult['data'])) {
                return [
                    'success' => true,
                    'message' => 'Test topup berhasil',
                    'data' => $topUpResult['data'],
                    'product_info' => $product
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal melakukan topup',
                'response' => $topUpResult
            ];

        } catch (\Exception $e) {
            Log::error('Digiflazz test topup exception', [
                'message' => $e->getMessage(),
                'buyer_sku_code' => $buyerSkuCode,
                'customer_no' => $customerNo
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test topup dengan berbagai skenario
     */
    public function runTopUpTests()
    {
        $tests = [];
        
        // Test 1: Cek koneksi
        $tests['connection'] = $this->testConnection();
        
        // Test 2: Cek saldo
        $balance = $this->checkBalance();
        $tests['balance'] = [
            'success' => $balance && isset($balance['data']),
            'message' => $balance ? 'Saldo berhasil dicek' : 'Gagal cek saldo',
            'data' => $balance
        ];
        
        // Test 3: Cek price list
        $priceList = $this->checkPrice();
        $tests['price_list'] = [
            'success' => $priceList && isset($priceList['data']),
            'message' => $priceList ? 'Price list berhasil diambil' : 'Gagal ambil price list',
            'data' => $priceList ? count($priceList['data']) . ' produk ditemukan' : null
        ];
        
        // Test 4: Coba topup dengan produk pertama yang aktif (jika ada)
        if ($priceList && isset($priceList['data']) && count($priceList['data']) > 0) {
            $activeProduct = null;
            foreach ($priceList['data'] as $product) {
                if ($product['status'] === 'active' && isset($product['buyer_sku_code'])) {
                    $activeProduct = $product;
                    break;
                }
            }
            
            if ($activeProduct) {
                $testRefId = 'TEST_' . time();
                $testCustomerNo = '08123456789'; // Nomor test
                
                $tests['topup_test'] = $this->testTopUp(
                    $activeProduct['buyer_sku_code'],
                    $testCustomerNo,
                    $testRefId
                );
            } else {
                $tests['topup_test'] = [
                    'success' => false,
                    'message' => 'Tidak ada produk aktif untuk testing'
                ];
            }
        } else {
            $tests['topup_test'] = [
                'success' => false,
                'message' => 'Tidak dapat melakukan test topup karena price list kosong'
            ];
        }
        
        return $tests;
    }

    /**
     * Test topup dengan produk spesifik
     */
    public function testSpecificProduct($buyerSkuCode, $customerNo = '08123456789')
    {
        $refId = 'TEST_SPECIFIC_' . time();
        
        return $this->testTopUp($buyerSkuCode, $customerNo, $refId);
    }

    /**
     * Test status transaksi
     */
    public function testTransactionStatus($refId)
    {
        try {
            if (empty($refId)) {
                return [
                    'success' => false,
                    'message' => 'Reference ID tidak boleh kosong'
                ];
            }

            $status = $this->checkStatus($refId);

            if ($status && isset($status['data'])) {
                return [
                    'success' => true,
                    'message' => 'Status transaksi berhasil dicek',
                    'data' => $status['data']
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal cek status transaksi',
                'response' => $status
            ];

        } catch (\Exception $e) {
            Log::error('Digiflazz test status exception', [
                'message' => $e->getMessage(),
                'ref_id' => $refId
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test webhook signature validation
     */
    public function testWebhookSignature($testData = 'test_data', $testSignature = null)
    {
        try {
            if (!$this->isWebhookConfigured()) {
                return [
                    'success' => false,
                    'message' => 'Webhook belum dikonfigurasi'
                ];
            }

            $expectedSignature = md5($this->webhookId . $this->secret . $testData);
            
            if ($testSignature === null) {
                $testSignature = $expectedSignature;
            }

            $isValid = $this->validateCallbackSignature($testData, $testSignature);

            return [
                'success' => true,
                'message' => 'Test webhook signature selesai',
                'data' => [
                    'is_valid' => $isValid,
                    'expected_signature' => $expectedSignature,
                    'provided_signature' => $testSignature,
                    'test_data' => $testData
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Top up produk untuk production (tanpa testing mode)
     */
    public function topUpProduction($buyerSkuCode, $customerNo, $refId = null, $maxPrice = null, $callbackUrl = null, $allowDot = false)
    {
        return $this->topUp($buyerSkuCode, $customerNo, $refId, false, $maxPrice, $callbackUrl, $allowDot);
    }

    /**
     * Top up produk untuk development (dengan testing mode)
     */
    public function topUpDevelopment($buyerSkuCode, $customerNo, $refId = null, $maxPrice = null, $callbackUrl = null, $allowDot = false)
    {
        return $this->topUp($buyerSkuCode, $customerNo, $refId, true, $maxPrice, $callbackUrl, $allowDot);
    }

    /**
     * Test topup untuk production (tanpa testing flag)
     */
    public function testTopUpProduction($buyerSkuCode, $customerNo, $refId = null)
    {
        try {
            // Validasi input
            if (empty($buyerSkuCode)) {
                return [
                    'success' => false,
                    'message' => 'Buyer SKU Code tidak boleh kosong'
                ];
            }

            if (empty($customerNo)) {
                return [
                    'success' => false,
                    'message' => 'Customer Number tidak boleh kosong'
                ];
            }

            // Cek apakah produk tersedia
            $priceCheck = $this->checkPrice($buyerSkuCode);
            
            if (!$priceCheck || !isset($priceCheck['data'])) {
                return [
                    'success' => false,
                    'message' => 'Produk tidak ditemukan atau tidak tersedia'
                ];
            }

            // Cari produk yang sesuai
            $product = null;
            foreach ($priceCheck['data'] as $item) {
                if ($item['buyer_sku_code'] === $buyerSkuCode) {
                    $product = $item;
                    break;
                }
            }

            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Produk dengan SKU ' . $buyerSkuCode . ' tidak ditemukan'
                ];
            }

            // Log product info untuk debugging
            Log::info('Product found for production topup test', [
                'buyer_sku_code' => $buyerSkuCode,
                'product' => $product,
                'mode' => 'production'
            ]);

            // Cek status produk (jika ada)
            if (isset($product['status']) && $product['status'] !== 'active') {
                return [
                    'success' => false,
                    'message' => 'Produk tidak aktif: ' . $product['status']
                ];
            }

            // Lakukan topup production (tanpa testing flag)
            $topUpResult = $this->topUpProduction($buyerSkuCode, $customerNo, $refId);

            if ($topUpResult && isset($topUpResult['data'])) {
                return [
                    'success' => true,
                    'message' => 'Test topup production berhasil',
                    'data' => $topUpResult['data'],
                    'product_info' => $product
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal melakukan topup production',
                'response' => $topUpResult
            ];

        } catch (\Exception $e) {
            Log::error('Digiflazz test topup production exception', [
                'message' => $e->getMessage(),
                'buyer_sku_code' => $buyerSkuCode,
                'customer_no' => $customerNo
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
