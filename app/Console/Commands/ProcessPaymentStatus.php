<?php

namespace App\Console\Commands;

use App\Services\PaymentStatusService;
use Illuminate\Console\Command;

class ProcessPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:process-status {--cleanup : Clean up old failed transactions} {--stats : Show payment statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process payment status and handle expired transactions';

    /**
     * Execute the console command.
     */
    public function handle(PaymentStatusService $paymentStatusService)
    {
        $this->info('Processing payment status...');

        // Process expired transactions
        $result = $paymentStatusService->processExpiredTransactions();
        
        if ($result['success']) {
            $this->info('✅ Processed ' . $result['processed_count'] . ' expired transactions');
        } else {
            $this->error('❌ Error processing expired transactions: ' . $result['message']);
            return 1;
        }

        // Show statistics if requested
        if ($this->option('stats')) {
            $this->showPaymentStatistics($paymentStatusService);
        }

        // Clean up old transactions if requested
        if ($this->option('cleanup')) {
            $this->cleanupOldTransactions($paymentStatusService);
        }

        $this->info('🎉 Payment status processing completed!');
        return 0;
    }

    /**
     * Show payment statistics
     */
    protected function showPaymentStatistics(PaymentStatusService $paymentStatusService)
    {
        $this->info('📊 Payment Statistics:');
        
        $stats = $paymentStatusService->getPaymentStatistics();
        
        if ($stats['success']) {
            $data = $stats['data'];
            
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Purchases', $data['total_purchases']],
                    ['Pending Payments', $data['pending_payments']],
                    ['Paid Payments', $data['paid_payments']],
                    ['Failed Payments', $data['failed_payments']],
                    ['Expired Payments', $data['expired_payments']],
                    ['Total Revenue', 'Rp ' . number_format($data['total_revenue'], 0, ',', '.')],
                    ['Today Revenue', 'Rp ' . number_format($data['today_revenue'], 0, ',', '.')]
                ]
            );
        } else {
            $this->error('❌ Error getting payment statistics: ' . $stats['message']);
        }
    }

    /**
     * Clean up old failed transactions
     */
    protected function cleanupOldTransactions(PaymentStatusService $paymentStatusService)
    {
        $this->info('🧹 Cleaning up old failed transactions...');
        
        $result = $paymentStatusService->cleanupOldFailedTransactions(30);
        
        if ($result['success']) {
            $this->info('✅ Cleaned up ' . $result['deleted_count'] . ' old failed transactions');
        } else {
            $this->error('❌ Error cleaning up old transactions: ' . $result['message']);
        }
    }
}
