<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Topup;
use App\Models\Configuration;
use App\Helpers\FonnteHelper;
use App\Services\TripayService;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class TripayController extends Controller
{
    protected $tripayService;
    protected $digiflazzService;

    public function __construct(TripayService $tripayService, DigiflazzService $digiflazzService)
    {
        $this->tripayService = $tripayService;
        $this->digiflazzService = $digiflazzService;
    }

    /**
     * Handle Tripay callback/webhook with enhanced error handling
     */
    public function callback(Request $request)
    {
        try {

            // Validate callback data
            $data = $request->all();
            $signature = $request->header('X-Callback-Signature');

            // Check if required data is present
            if (empty($data) || empty($signature)) {
                Log::error('Tripay Callback: Missing required data or signature', [
                    'data' => $data,
                    'signature' => $signature
                ]);
                return response()->json(['success' => false, 'message' => 'Data callback tidak lengkap'], 400);
            }

            // Get raw JSON data for signature verification
            $rawJsonData = $request->getContent();
            
            // Verify signature using raw JSON data (as per Tripay documentation)
            if (!$this->tripayService->verifyCallbackWithRawJson($rawJsonData, $signature)) {
                Log::error('Tripay Callback: Invalid signature', [
                    'data' => $data,
                    'signature' => $signature
                ]);
                return response()->json(['success' => false, 'message' => 'Signature tidak valid'], 400);
            }

            // Validate required callback fields
            $requiredFields = ['merchant_ref', 'amount', 'status', 'reference'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    Log::error('Tripay Callback: Missing required field: ' . $field, ['data' => $data]);
                    return response()->json(['success' => false, 'message' => 'Field ' . $field . ' tidak ditemukan'], 400);
                }
            }

            // Find purchase by merchant_ref (order_id)
            $purchase = Purchase::where('order_id', $data['merchant_ref'])->first();

            if (!$purchase) {
                // Cek apakah ini adalah topup
                $topup = Topup::where('topup_id', $data['merchant_ref'])->first();
                
                if ($topup) {
                    // Handle topup callback
                    return $this->handleTopupCallback($data, $topup);
                } else {
                    Log::error('Tripay Callback: Purchase/Topup not found', ['merchant_ref' => $data['merchant_ref']]);
                    return response()->json(['success' => false, 'message' => 'Pesanan/Topup tidak ditemukan'], 404);
                }
            }

            // Check if purchase is already processed
            if ($purchase->payment_status === 'paid' && $purchase->status === 'completed') {
                
                return response()->json(['success' => true, 'message' => 'Pesanan sudah diproses']);
            }

            // Update purchase status based on Tripay status
            $status = $this->mapTripayStatus($data['status']);
            $paymentStatus = $this->mapTripayPaymentStatus($data['status']);

            // Prepare notes update
            $currentNotes = json_decode($purchase->notes, true) ?? [];
            $newNotes = array_merge($currentNotes, [
                'tripay_reference' => $data['reference'] ?? null,
                'tripay_status' => $data['status'] ?? null,
                'tripay_amount' => $data['amount'] ?? null,
                'tripay_fee' => $data['fee_merchant'] ?? null,
                'callback_received_at' => now()->format('Y-m-d H:i:s'),
                'callback_data' => $data
            ]);

            $purchase->update([
                'status' => $status,
                'payment_status' => $paymentStatus,
                'processed_at' => $paymentStatus === 'paid' ? now() : null,
                'tripay_reference' => $data['reference'] ?? $purchase->tripay_reference,
                'notes' => json_encode($newNotes)
            ]);



            // Send notification if payment is successful
            if ($paymentStatus === 'paid') {
                $this->sendPaymentSuccessNotification($purchase);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Tripay Callback Error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal'], 500);
        }
    }

    /**
     * Handle topup callback from Tripay
     */
    protected function handleTopupCallback($data, $topup)
    {
        try {
            // Check if topup is already processed
            if ($topup->status === 'success') {
                return response()->json(['success' => true, 'message' => 'Topup sudah diproses']);
            }

            // Update topup status based on Tripay status
            $status = $this->mapTripayStatus($data['status']);
            
            // Prepare notes update
            $currentNotes = json_decode($topup->payment_notes, true) ?? [];
            $newNotes = array_merge($currentNotes, [
                'tripay_reference' => $data['reference'] ?? null,
                'tripay_status' => $data['status'] ?? null,
                'tripay_amount' => $data['amount'] ?? null,
                'tripay_fee' => $data['fee_merchant'] ?? null,
                'callback_received_at' => now()->format('Y-m-d H:i:s'),
                'callback_data' => $data
            ]);

            $topup->update([
                'status' => $status,
                'tripay_reference' => $data['reference'] ?? $topup->tripay_reference,
                'payment_notes' => json_encode($newNotes)
            ]);

            // If payment is successful, add balance to member and send notification
            if ($status === 'success') {
                if ($topup->member_id) {
                    $member = \App\Models\Member::find($topup->member_id);
                    if ($member) {
                        $member->increment('balance', $topup->jumlah);
                        
                        // Kirim notifikasi WhatsApp untuk topup berhasil
                        if (FonnteHelper::isConfigured() && $member->phone) {
                            try {
                                // Refresh member data untuk mendapatkan balance terbaru
                                $member->refresh();
                                
                                $topupData = [
                                    'username' => $member->username,
                                    'topup_id' => $topup->topup_id,
                                    'amount' => $topup->jumlah,
                                    'balance' => $member->balance
                                ];
                                
                                FonnteHelper::sendTopupSuccessNotification($member->phone, $topupData);
                            } catch (\Exception $e) {
                                Log::error('Failed to send topup success WhatsApp notification via Tripay callback', [
                                    'error' => $e->getMessage(),
                                    'member_id' => $member->id,
                                    'topup_id' => $topup->topup_id
                                ]);
                            }
                        }
                    }
                }
            }

            return response()->json(['success' => true, 'message' => 'Topup callback processed successfully']);

        } catch (\Exception $e) {
            Log::error('Topup Callback Error: ' . $e->getMessage(), [
                'topup_id' => $topup->topup_id ?? null,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan internal'], 500);
        }
    }

    /**
     * Get transaction status from Tripay with retry mechanism
     */
    public function checkStatus(Request $request)
    {
        try {
            $request->validate([
                'reference' => 'required|string|max:255'
            ]);

            // Check if Tripay is configured
            if (!$this->tripayService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway tidak dikonfigurasi'
                ], 503);
            }

            $result = $this->tripayService->getTransactionDetail($request->reference);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

            // Update local purchase status if needed
            $this->syncPurchaseStatus($request->reference, $result['data']);

            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            Log::error('Tripay Check Status Error: ' . $e->getMessage(), [
                'reference' => $request->reference ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal'
            ], 500);
        }
    }

    /**
     * Get available payment methods from Tripay
     */
    public function getPaymentMethods()
    {
        try {
            // Check if Tripay is configured
            if (!$this->tripayService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway tidak dikonfigurasi'
                ], 503);
            }

            $result = $this->tripayService->getPaymentMethods();

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            Log::error('Tripay Get Payment Methods Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal'
            ], 500);
        }
    }

    /**
     * Test Tripay connection
     */
    public function testConnection()
    {
        try {
            $result = $this->tripayService->testConnection();

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Tripay Test Connection Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error koneksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync purchase status with Tripay data
     */
    protected function syncPurchaseStatus($reference, $tripayData)
    {
        try {
            $purchase = Purchase::where('tripay_reference', $reference)->first();
            
            if (!$purchase) {
                Log::warning('Tripay Sync: Purchase not found for reference: ' . $reference);
                return;
            }

            $status = $this->mapTripayStatus($tripayData['status']);
            $paymentStatus = $this->mapTripayPaymentStatus($tripayData['status']);

            // Only update if status has changed
            if ($purchase->status !== $status || $purchase->payment_status !== $paymentStatus) {
                $purchase->update([
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'processed_at' => $paymentStatus === 'paid' ? now() : null
                ]);


            }
        } catch (\Exception $e) {
            Log::error('Tripay Sync Error: ' . $e->getMessage());
        }
    }

    /**
     * Send payment success notification and process Digiflazz top-up
     */
    protected function sendPaymentSuccessNotification($purchase)
    {
        try {
            Log::info('Payment Success Notification: Processing order ' . $purchase->order_id);
            
            // Process Digiflazz top-up if product has provider
            if ($purchase->product && $purchase->product->provider === 'Digiflazz') {
                $this->processDigiflazzTopUp($purchase);
            }
            
            // Here you can implement additional notification logic
            // For example: send WhatsApp message, email, etc.
            // $this->sendWhatsAppNotification($purchase);
            
        } catch (\Exception $e) {
            Log::error('Payment Success Notification Error: ' . $e->getMessage());
        }
    }

    /**
     * Process Digiflazz top-up after successful payment
     */
    protected function processDigiflazzTopUp($purchase)
    {
        try {
            // Check if Digiflazz is configured
            if (!$this->digiflazzService->isConfigured()) {
                Log::error('Digiflazz Top-up: Provider tidak dikonfigurasi untuk order ' . $purchase->order_id);
                return;
            }

            // Get player data from notes
            $notes = json_decode($purchase->notes, true) ?? [];
            $playerFields = $notes['player_fields'] ?? [];
            
            if (empty($playerFields)) {
                Log::error('Digiflazz Top-up: Player data tidak ditemukan untuk order ' . $purchase->order_id);
                return;
            }

            // Get customer number (first player field)
            $customerNo = $playerFields[0];
            
            // Get product SKU code
            $buyerSkuCode = $purchase->product->sku ?? null;
            
            if (!$buyerSkuCode) {
                Log::error('Digiflazz Top-up: SKU code tidak ditemukan untuk produk ' . $purchase->product->name);
                return;
            }

            // Generate ref_id for Digiflazz
            $refId = 'REF' . time() . rand(100, 999);

            Log::info('Digiflazz Top-up: Processing top-up', [
                'order_id' => $purchase->order_id,
                'buyer_sku_code' => $buyerSkuCode,
                'customer_no' => $customerNo,
                'ref_id' => $refId
            ]);

            // Process top-up via Digiflazz
            $result = $this->digiflazzService->topUp($buyerSkuCode, $customerNo, $refId);

            if ($result) {
                // Update purchase with Digiflazz data
                $digiflazzNotes = array_merge($notes, [
                    'digiflazz_ref_id' => $refId,
                    'digiflazz_status' => $result['status'] ?? 'pending',
                    'digiflazz_message' => $result['message'] ?? '',
                    'digiflazz_buyer_sku_code' => $buyerSkuCode,
                    'digiflazz_customer_no' => $customerNo,
                    'digiflazz_processed_at' => now()->format('Y-m-d H:i:s')
                ]);

                $purchase->update([
                    'digiflazz_ref_id' => $refId,
                    'digiflazz_status' => $result['status'] ?? 'pending',
                    'digiflazz_message' => $result['message'] ?? '',
                    'digiflazz_buyer_sku_code' => $buyerSkuCode,
                    'digiflazz_customer_no' => $customerNo,
                    'notes' => json_encode($digiflazzNotes)
                ]);

                Log::info('Digiflazz Top-up: Success', [
                    'order_id' => $purchase->order_id,
                    'ref_id' => $refId,
                    'status' => $result['status'] ?? 'pending',
                    'message' => $result['message'] ?? ''
                ]);
            } else {
                Log::error('Digiflazz Top-up: Failed for order ' . $purchase->order_id);
            }

        } catch (\Exception $e) {
            Log::error('Digiflazz Top-up Error: ' . $e->getMessage(), [
                'order_id' => $purchase->order_id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Map Tripay status to purchase status with enhanced mapping
     */
    protected function mapTripayStatus($tripayStatus)
    {
        $statusMap = [
            'UNPAID' => 'pending',
            'PAID' => 'completed',
            'EXPIRED' => 'failed',
            'FAILED' => 'failed',
            'CANCELLED' => 'cancelled',
            'REFUND' => 'cancelled',
            'REFUNDED' => 'cancelled',
            'PROCESSING' => 'processing',
            'PENDING' => 'pending'
        ];

        $status = $statusMap[$tripayStatus] ?? 'pending';
        

        
        return $status;
    }

    /**
     * Map Tripay status to payment status with enhanced mapping
     */
    protected function mapTripayPaymentStatus($tripayStatus)
    {
        $paymentStatusMap = [
            'UNPAID' => 'pending',
            'PAID' => 'paid',
            'EXPIRED' => 'expired',
            'FAILED' => 'failed',
            'CANCELLED' => 'failed',
            'REFUND' => 'failed',
            'REFUNDED' => 'failed',
            'PROCESSING' => 'pending',
            'PENDING' => 'pending'
        ];

        $paymentStatus = $paymentStatusMap[$tripayStatus] ?? 'pending';
        

        
        return $paymentStatus;
    }
}
