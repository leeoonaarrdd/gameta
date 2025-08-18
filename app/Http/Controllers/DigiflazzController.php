<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Models\Purchase;
use App\Services\DigiflazzService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class DigiflazzController extends Controller
{
    protected $digiflazzService;

    public function __construct(DigiflazzService $digiflazzService)
    {
        $this->digiflazzService = $digiflazzService;
    }

    public function callback(Request $request)
    {
        // Log callback data untuk debugging
        Log::info('Digiflazz callback received', $request->all());
        
        try {
            // Cek apakah webhook sudah dikonfigurasi
            if (!$this->digiflazzService->isWebhookConfigured()) {
                Log::warning('Digiflazz webhook not configured, skipping signature validation');
                // Untuk development, kita bisa skip signature validation
                // Tapi tetap log warning untuk production
            } else {
                // Validasi signature hanya jika webhook dikonfigurasi
                $signature = $request->header('X-Signature');
                $data = $request->getContent();
                
                if (!$this->digiflazzService->validateCallbackSignature($data, $signature)) {
                    Log::warning('Digiflazz callback signature validation failed');
                    return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
                }
            }

            $callbackData = $request->all();
            
            // Proses callback berdasarkan status
            $refId = $callbackData['ref_id'] ?? null;
            $status = $callbackData['status'] ?? null;
            $message = $callbackData['message'] ?? '';
            
            if ($refId) {
                // Cari purchase berdasarkan ref_id
                $purchase = Purchase::where('digiflazz_ref_id', $refId)->first();
                
                if ($purchase) {
                    // Update status purchase berdasarkan callback
                    switch ($status) {
                        case 'success':
                            $purchase->update([
                                'status' => 'completed',
                                'digiflazz_status' => $status,
                                'digiflazz_message' => $message,
                                'completed_at' => now()
                            ]);
                            break;
                            
                        case 'pending':
                            $purchase->update([
                                'status' => 'pending',
                                'digiflazz_status' => $status,
                                'digiflazz_message' => $message
                            ]);
                            break;
                            
                        case 'error':
                        case 'failed':
                            $purchase->update([
                                'status' => 'failed',
                                'digiflazz_status' => $status,
                                'digiflazz_message' => $message
                            ]);
                            break;
                    }
                    
                    Log::info('Purchase status updated via Digiflazz callback', [
                        'purchase_id' => $purchase->id,
                        'ref_id' => $refId,
                        'status' => $status
                    ]);
                } else {
                    Log::warning('Purchase not found for Digiflazz ref_id', ['ref_id' => $refId]);
                }
            }
            
            return response()->json(['status' => 'success']);
            
        } catch (\Exception $e) {
            Log::error('Digiflazz callback processing error', [
                'message' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    public function testConnection()
    {
        try {
            if (!$this->digiflazzService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konfigurasi Digiflazz belum lengkap'
                ]);
            }

            $result = $this->digiflazzService->testConnection();
            return response()->json($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function checkBalance()
    {
        try {
            if (!$this->digiflazzService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konfigurasi Digiflazz belum lengkap'
                ]);
            }

            $balance = $this->digiflazzService->checkBalance();
            
            if ($balance) {
                return response()->json([
                    'success' => true,
                    'data' => $balance
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data saldo'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function checkPrice($productCode = null)
    {
        try {
            if (!$this->digiflazzService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konfigurasi Digiflazz belum lengkap'
                ]);
            }

            $prices = $this->digiflazzService->checkPrice($productCode);
            
            if ($prices) {
                return response()->json([
                    'success' => true,
                    'data' => $prices
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data harga'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }


}
