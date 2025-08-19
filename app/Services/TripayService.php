<?php

namespace App\Services;

use App\Models\Configuration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TripayService
{
    protected $apiKey;
    protected $privateKey;
    protected $merchantCode;
    protected $callbackUrl;
    protected $baseUrl;
    protected $isConfigured;

    public function __construct()
    {
        $this->apiKey = Configuration::getValue('tripay_api_key');
        $this->privateKey = Configuration::getValue('tripay_private_key');
        $this->merchantCode = Configuration::getValue('tripay_merchant_code');
        $this->callbackUrl = url('/api/tripay/callback');
        
        // Use sandbox URL for development, production URL for production
        $this->baseUrl = config('app.env') === 'production' 
            ? 'https://tripay.co.id/api/'
            : 'https://tripay.co.id/api-sandbox/';
            
        // Check if Tripay is properly configured
        $this->isConfigured = $this->validateConfiguration();
            

    }

    /**
     * Validate Tripay configuration
     */
    protected function validateConfiguration()
    {
        if (empty($this->apiKey) || empty($this->privateKey) || empty($this->merchantCode)) {
            Log::warning('Tripay Configuration: Missing required configuration parameters');
            return false;
        }

        // Validate API key format (should be a valid string)
        if (!is_string($this->apiKey) || strlen($this->apiKey) < 10) {
            Log::warning('Tripay Configuration: Invalid API key format');
            return false;
        }

        // Validate private key format (should be a valid string)
        if (!is_string($this->privateKey) || strlen($this->privateKey) < 10) {
            Log::warning('Tripay Configuration: Invalid private key format');
            return false;
        }

        // Validate merchant code format
        if (!is_string($this->merchantCode) || strlen($this->merchantCode) < 3) {
            Log::warning('Tripay Configuration: Invalid merchant code format');
            return false;
        }

        return true;
    }

    /**
     * Create payment transaction
     */
    public function createTransaction($orderData)
    {
        try {
            // Check if Tripay is configured
            if (!$this->isConfigured) {
                Log::error('Tripay Service: Payment gateway tidak dikonfigurasi');
                return [
                    'success' => false,
                    'message' => 'Payment gateway tidak dikonfigurasi. Silakan hubungi administrator.'
                ];
            }

            // Validate required order data
            $requiredFields = ['order_id', 'total', 'product_name', 'whatsapp'];
            foreach ($requiredFields as $field) {
                if (empty($orderData[$field])) {
                    Log::error('Tripay Service: Missing required field: ' . $field);
                    return [
                        'success' => false,
                        'message' => 'Data pesanan tidak lengkap: ' . $field
                    ];
                }
            }

            // Generate signature first
            $signature = $this->generateSignature($orderData['order_id'], $orderData['total']);
            
            // Calculate admin fee and unique code for order items
            $adminFee = $orderData['admin_fee'] ?? 0;
            $uniqueCode = $orderData['unique_code'] ?? 0;
            
            // Create order items array with product and fees
            $orderItems = [
                [
                    'name' => $orderData['product_name'],
                    'price' => (int) $orderData['price'],
                    'quantity' => 1,
                ]
            ];
            
            // Add admin fee as separate item if exists
            if ($adminFee > 0) {
                $orderItems[] = [
                    'name' => 'Biaya Admin',
                    'price' => (int) $adminFee,
                    'quantity' => 1,
                ];
            }
            
            // Add unique code as separate item if exists
            if ($uniqueCode > 0) {
                $orderItems[] = [
                    'name' => 'Kode Unik',
                    'price' => (int) $uniqueCode,
                    'quantity' => 1,
                ];
            }
            
            $data = [
                'method' => $this->getPaymentMethod($orderData['payment_method']),
                'merchant_ref' => $orderData['order_id'],
                'amount' => (int) $orderData['total'],
                'customer_name' => $orderData['player_nickname'] ?? 'Customer',
                'customer_email' => 'customer@example.com',
                'customer_phone' => $orderData['whatsapp'],
                'order_items' => $orderItems,
                'return_url' => route('checkout.payment', ['order_id' => $orderData['order_id']]),
                'callback_url' => $this->callbackUrl,
                'expired_time' => (int) strtotime($orderData['expired_at']),
                'signature' => $signature
            ];


            
            $response = Http::timeout(30)
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Force IPv4
                    ]
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json'
                ])->post($this->baseUrl . 'transaction/create', $data);

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['success']) {
                    return [
                        'success' => true,
                        'data' => $result['data'],
                        'payment_url' => $result['data']['checkout_url'],
                        'qr_code' => $result['data']['qr_url'] ?? null,
                        'merchant_ref' => $result['data']['merchant_ref'],
                        'reference' => $result['data']['reference']
                    ];
                } else {
                    Log::error('Tripay API Error: ' . json_encode($result));
                    return [
                        'success' => false,
                        'message' => $result['message'] ?? 'Gagal membuat transaksi pembayaran'
                    ];
                }
            } else {
                Log::error('Tripay HTTP Error: ' . $response->status() . ' - ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'Gagal terhubung ke payment gateway. Silakan coba lagi.'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Tripay Service Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'order_data' => $orderData
            ]);
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan internal. Silakan coba lagi.'
            ];
        }
    }

    /**
     * Get transaction detail with retry mechanism
     */
    public function getTransactionDetail($reference, $retryCount = 0)
    {
        try {
            if (!$this->isConfigured) {
                return [
                    'success' => false,
                    'message' => 'Payment gateway tidak dikonfigurasi'
                ];
            }

            $response = Http::timeout(30)
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Force IPv4
                    ]
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json'
                ])->get($this->baseUrl . 'transaction/detail', [
                    'reference' => $reference
                ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['success']) {
                    return [
                        'success' => true,
                        'data' => $result['data']
                    ];
                } else {
                    Log::error('Tripay API Error: ' . json_encode($result));
                    return [
                        'success' => false,
                        'message' => $result['message'] ?? 'Gagal mendapatkan detail transaksi'
                    ];
                }
            } else {
                // Retry mechanism for network errors
                if ($retryCount < 3 && $response->status() >= 500) {
                    Log::warning('Tripay HTTP Error, retrying... Attempt: ' . ($retryCount + 1));
                    sleep(2); // Wait 2 seconds before retry
                    return $this->getTransactionDetail($reference, $retryCount + 1);
                }

                Log::error('Tripay HTTP Error: ' . $response->status() . ' - ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'Gagal terhubung ke payment gateway'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Tripay Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan internal'
            ];
        }
    }

    /**
     * Verify callback signature with enhanced security
     */
    public function verifyCallback($data, $signature)
    {
        try {
            if (!$this->isConfigured) {
                Log::error('Tripay Callback: Payment gateway tidak dikonfigurasi');
                return false;
            }

            // Validate required callback data
            if (!isset($data['merchant_ref'])) {
                Log::error('Tripay Callback: Missing merchant_ref');
                return false;
            }
            
            // Handle amount field - Tripay sends total_amount or amount_received
            $amount = null;
            if (isset($data['total_amount'])) {
                $amount = $data['total_amount'];
            } elseif (isset($data['amount_received'])) {
                $amount = $data['amount_received'];
            } elseif (isset($data['amount'])) {
                $amount = $data['amount'];
            }
            
            if ($amount === null || !is_numeric($amount)) {
                Log::error('Tripay Callback: Missing or invalid amount field', [
                    'total_amount' => $data['total_amount'] ?? null,
                    'amount_received' => $data['amount_received'] ?? null,
                    'amount' => $data['amount'] ?? null
                ]);
                return false;
            }

            // Generate expected signature using merchant_ref + amount
            $signatureString = $data['merchant_ref'] . $data['amount'];
            $expectedSignature = hash_hmac('sha256', $signatureString, $this->privateKey);

            // Use hash_equals for timing attack protection
            $isValid = hash_equals($expectedSignature, $signature);



            return $isValid;
        } catch (\Exception $e) {
            Log::error('Tripay Callback Verification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify callback signature using raw JSON data (as per Tripay documentation)
     */
    public function verifyCallbackWithRawJson($rawJsonData, $signature)
    {
        try {
            if (!$this->isConfigured) {
                Log::error('Tripay Callback: Payment gateway tidak dikonfigurasi');
                return false;
            }

            if (empty($rawJsonData)) {
                Log::error('Tripay Callback: No raw JSON data provided');
                return false;
            }

            // Generate expected signature using raw JSON data (as per Tripay documentation)
            $expectedSignature = $this->generateCallbackSignature($rawJsonData);

            // Use hash_equals for timing attack protection
            $isValid = hash_equals($expectedSignature, $signature);

            // Log signature verification details for debugging
            Log::info('Tripay Callback: Signature verification details', [
                'raw_json_data' => $rawJsonData,
                'received_signature' => $signature,
                'expected_signature' => $expectedSignature,
                'is_valid' => $isValid,
                'private_key_length' => strlen($this->privateKey)
            ]);

            return $isValid;
        } catch (\Exception $e) {
            Log::error('Tripay Callback Verification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get available payment methods with caching
     */
    public function getPaymentMethods()
    {
        try {
            if (!$this->isConfigured) {
                return [
                    'success' => false,
                    'message' => 'Payment gateway tidak dikonfigurasi'
                ];
            }

            $response = Http::timeout(30)
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Force IPv4
                    ]
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json'
                ])->get($this->baseUrl . 'merchant/payment-channel');

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['success']) {
                    return [
                        'success' => true,
                        'data' => $result['data']
                    ];
                } else {
                    Log::error('Tripay API Error: ' . json_encode($result));
                    return [
                        'success' => false,
                        'message' => $result['message'] ?? 'Gagal mendapatkan metode pembayaran'
                    ];
                }
            } else {
                Log::error('Tripay HTTP Error: ' . $response->status() . ' - ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'Gagal terhubung ke payment gateway'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Tripay Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan internal'
            ];
        }
    }

    /**
     * Map payment method to Tripay method code with enhanced mapping
     */
    protected function getPaymentMethod($paymentMethodName)
    {
        // Enhanced payment method mapping
        $methodMap = [
            // Bank Transfer
            'Bank Transfer' => 'BRIVA',
            'Bank Transfer BCA' => 'BCAVA',
            'Bank Transfer BNI' => 'BNIVA',
            'Bank Transfer BRI' => 'BRIVA',
            'Bank Transfer Mandiri' => 'MANDIRI',
            
            // E-Wallet
            'E-Wallet' => 'OVO',
            'DANA' => 'DANA',
            'OVO' => 'OVO',
            'GoPay' => 'GOPAY',
            'ShopeePay' => 'SHOPEEPAY',
            'LinkAja' => 'LINKAJA',
            
            // QRIS
            'QRIS' => 'QRIS',
            'QRIS Payment' => 'QRIS',
            
            // Virtual Account
            'Virtual Account' => 'BRIVA',
            'VA BCA' => 'BCAVA',
            'BCA Virtual Account' => 'BCAVA',
            'VA BNI' => 'BNIVA',
            'BNI Virtual Account' => 'BNIVA',
            'VA BRI' => 'BRIVA',
            'BRI Virtual Account' => 'BRIVA',
            'VA Mandiri' => 'MANDIRI',
            'Mandiri Virtual Account' => 'MANDIRI',
            
            // Convenience Store
            'Convenience Store' => 'ALFAMART',
            'Indomaret' => 'INDOMARET',
            'Alfamart' => 'ALFAMART',
            
            // Credit Card
            'Credit Card' => 'CC',
            'Visa' => 'CC',
            'Mastercard' => 'CC',
            
            // Other
            'COD' => 'COD',
            'Cash on Delivery' => 'COD'
        ];

        // Try to find exact match first
        if (isset($methodMap[$paymentMethodName])) {
            return $methodMap[$paymentMethodName];
        }

        // Try partial match (case insensitive)
        foreach ($methodMap as $key => $value) {
            if (stripos($paymentMethodName, $key) !== false) {
                return $value;
            }
        }

        // Default to QRIS if no match found
        return 'QRIS';
    }

    /**
     * Generate signature for transaction with enhanced security
     */
    protected function generateSignature($merchantRef, $amount)
    {
        try {
            // Validate input parameters
            if (empty($merchantRef) || empty($amount)) {
                throw new \Exception('Merchant reference and amount are required for signature generation');
            }

            // Tripay signature format: merchantCode + merchantRef + amount (as per Tripay documentation)
            $signatureString = $this->merchantCode . $merchantRef . $amount;
            
            // Generate signature using HMAC SHA256
            $signature = hash_hmac('sha256', $signatureString, $this->privateKey);
            

            
            return $signature;
        } catch (\Exception $e) {
            Log::error('Tripay Signature Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate signature for callback verification (using raw JSON data)
     */
    public function generateCallbackSignature($jsonData)
    {
        try {
            // Validate input parameters
            if (empty($jsonData)) {
                throw new \Exception('JSON data is required for callback signature generation');
            }

            // Generate signature using HMAC SHA256 with raw JSON data
            $signature = hash_hmac('sha256', $jsonData, $this->privateKey);
            

            
            return $signature;
        } catch (\Exception $e) {
            Log::error('Tripay Callback Signature Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if Tripay is properly configured
     */
    public function isConfigured()
    {
        return $this->isConfigured;
    }

    /**
     * Test Tripay connection
     */
    public function testConnection()
    {
        try {
            if (!$this->isConfigured) {
                return [
                    'success' => false,
                    'message' => 'Payment gateway tidak dikonfigurasi'
                ];
            }

            $response = Http::timeout(10)
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, // Force IPv4
                    ]
                ])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json'
                ])->get($this->baseUrl . 'merchant/payment-channel');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Koneksi ke Tripay berhasil'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal terhubung ke Tripay: ' . $response->status()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error koneksi: ' . $e->getMessage()
            ];
        }
    }
}
