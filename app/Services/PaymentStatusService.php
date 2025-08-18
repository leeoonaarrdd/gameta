<?php

namespace App\Services;

use App\Models\Purchase;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PaymentStatusService
{
    protected $tripayService;

    public function __construct(TripayService $tripayService)
    {
        $this->tripayService = $tripayService;
    }

    /**
     * Process expired transactions
     */
    public function processExpiredTransactions()
    {
        try {
            $expiredPurchases = Purchase::where('status', 'pending')
                ->where('payment_status', 'pending')
                ->where('created_at', '<=', now()->subMinutes(30))
                ->get();

            $processedCount = 0;

            foreach ($expiredPurchases as $purchase) {
                $this->expirePurchase($purchase);
                $processedCount++;
            }

            Log::info('Payment Status Service: Processed ' . $processedCount . ' expired transactions');

            return [
                'success' => true,
                'processed_count' => $processedCount
            ];

        } catch (\Exception $e) {
            Log::error('Payment Status Service Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error processing expired transactions'
            ];
        }
    }

    /**
     * Expire a specific purchase
     */
    public function expirePurchase(Purchase $purchase)
    {
        try {
            // Check if it's a Tripay payment and sync status
            if ($purchase->tripay_reference && $this->tripayService->isConfigured()) {
                $result = $this->tripayService->getTransactionDetail($purchase->tripay_reference);
                
                if ($result['success']) {
                    $tripayStatus = $result['data']['status'];
                    
                    // Only expire if Tripay also shows expired
                    if ($tripayStatus === 'EXPIRED') {
                        $this->updatePurchaseStatus($purchase, 'failed', 'expired');

                    } else {
                        // Sync with Tripay status
                        $this->syncWithTripayStatus($purchase, $tripayStatus);
                    }
                } else {
                    // If can't get Tripay status, expire locally
                    $this->updatePurchaseStatus($purchase, 'failed', 'expired');
                    Log::warning('Payment Status Service: Could not get Tripay status, expiring locally', [
                        'order_id' => $purchase->order_id
                    ]);
                }
            } else {
                // For non-Tripay payments, expire directly
                $this->updatePurchaseStatus($purchase, 'failed', 'expired');

            }

        } catch (\Exception $e) {
            Log::error('Payment Status Service: Error expiring purchase ' . $purchase->order_id . ': ' . $e->getMessage());
        }
    }

    /**
     * Sync purchase status with Tripay
     */
    public function syncWithTripayStatus(Purchase $purchase, $tripayStatus)
    {
        try {
            $statusMap = [
                'PAID' => ['status' => 'completed', 'payment_status' => 'paid'],
                'EXPIRED' => ['status' => 'failed', 'payment_status' => 'expired'],
                'FAILED' => ['status' => 'failed', 'payment_status' => 'failed'],
                'CANCELLED' => ['status' => 'cancelled', 'payment_status' => 'failed'],
                'REFUND' => ['status' => 'cancelled', 'payment_status' => 'failed'],
                'REFUNDED' => ['status' => 'cancelled', 'payment_status' => 'failed'],
                'PROCESSING' => ['status' => 'processing', 'payment_status' => 'pending'],
                'PENDING' => ['status' => 'pending', 'payment_status' => 'pending'],
                'UNPAID' => ['status' => 'pending', 'payment_status' => 'pending']
            ];

            $newStatus = $statusMap[$tripayStatus] ?? ['status' => 'pending', 'payment_status' => 'pending'];

            $this->updatePurchaseStatus($purchase, $newStatus['status'], $newStatus['payment_status']);



        } catch (\Exception $e) {
            Log::error('Payment Status Service: Error syncing with Tripay status: ' . $e->getMessage());
        }
    }

    /**
     * Update purchase status
     */
    protected function updatePurchaseStatus(Purchase $purchase, $status, $paymentStatus)
    {
        $purchase->update([
            'status' => $status,
            'payment_status' => $paymentStatus,
            'processed_at' => $paymentStatus === 'paid' ? now() : null
        ]);

        // Add to notes
        $currentNotes = json_decode($purchase->notes, true) ?? [];
        $newNotes = array_merge($currentNotes, [
            'status_updated_at' => now()->format('Y-m-d H:i:s'),
            'status_update_reason' => 'system_auto_update'
        ]);

        $purchase->update(['notes' => json_encode($newNotes)]);
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStatistics()
    {
        try {
            $stats = [
                'total_purchases' => Purchase::count(),
                'pending_payments' => Purchase::where('payment_status', 'pending')->count(),
                'paid_payments' => Purchase::where('payment_status', 'paid')->count(),
                'failed_payments' => Purchase::where('payment_status', 'failed')->count(),
                'expired_payments' => Purchase::where('payment_status', 'expired')->count(),
                'total_revenue' => Purchase::where('payment_status', 'paid')->sum('total_amount'),
                'today_revenue' => Purchase::where('payment_status', 'paid')
                    ->whereDate('created_at', today())
                    ->sum('total_amount')
            ];

            return [
                'success' => true,
                'data' => $stats
            ];

        } catch (\Exception $e) {
            Log::error('Payment Status Service: Error getting statistics: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error getting payment statistics'
            ];
        }
    }

    /**
     * Clean up old failed transactions
     */
    public function cleanupOldFailedTransactions($days = 30)
    {
        try {
            $oldFailedPurchases = Purchase::where('status', 'failed')
                ->where('created_at', '<=', now()->subDays($days))
                ->get();

            $deletedCount = 0;

            foreach ($oldFailedPurchases as $purchase) {
                $purchase->delete();
                $deletedCount++;
            }

            Log::info('Payment Status Service: Cleaned up ' . $deletedCount . ' old failed transactions');

            return [
                'success' => true,
                'deleted_count' => $deletedCount
            ];

        } catch (\Exception $e) {
            Log::error('Payment Status Service: Error cleaning up old transactions: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error cleaning up old transactions'
            ];
        }
    }
}
