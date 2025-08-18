<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\Member;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodCategory;
use App\Models\Configuration;
use App\Services\TripayService;
use App\Helpers\FonnteHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;

class MemberTopupController extends Controller
{
    protected $tripayService;

    public function __construct(TripayService $tripayService)
    {
        $this->tripayService = $tripayService;
    }

    /**
     * Show the topup saldo page
     */
    public function showTopupSaldo()
    {
        $paymentMethods = PaymentMethod::with('category')
            ->where('is_active', true)
            ->where(function($query) {
                $query->where('provider', '!=', 'innerpay')
                      ->where('name', 'not like', '%innerpay%')
                      ->where('name', 'not like', '%balance%');
            })
            ->orderBy('kategori', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->groupBy('kategori');

        $paymentCategories = PaymentMethodCategory::all();

        return view('member.topup-saldo', compact('paymentMethods', 'paymentCategories'));
    }

    /**
     * Process topup request
     */
    public function processTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_method_id' => 'required|exists:payment_methods,id'
        ]);

        try {
            $member = Auth::guard('member')->user();
            
            // Get payment method details
            $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);
            
            // Check if payment method is balance payment (not allowed for topup)
            if ($this->isBalancePayment($paymentMethod)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode pembayaran saldo tidak dapat digunakan untuk topup saldo'
                ], 422);
            }
            
            // Get topup prefix from configuration
            $topupPrefix = Configuration::getValue('topup_prefix', 'TOPUP');
            
            // Generate unique topup ID with configured prefix
            $topupId = $topupPrefix . '-' . strtoupper(Str::random(8));
            
            // Check if payment method is Tripay
            $isTripay = $this->isTripayPayment($paymentMethod);
            
            // Initialize payment data
            $paymentData = [
                'username' => $member->username,
                'topup_id' => $topupId,
                'jumlah' => $request->amount,
                'status' => 'pending',
                'payment_method' => $paymentMethod->name,
                'member_id' => $member->id,
                'tanggal' => now()
            ];
            
            // Handle Tripay payment
            if ($isTripay) {
                // Create Tripay transaction
                $tripayResult = $this->tripayService->createTransaction([
                    'order_id' => $topupId,
                    'total' => $request->amount,
                    'price' => $request->amount,
                    'product_name' => 'Topup Saldo',
                    'payment_method' => $paymentMethod->method_code,
                    'whatsapp' => $member->phone ?? '08123456789',
                    'player_nickname' => $member->username,
                    'admin_fee' => 0,
                    'unique_code' => 0,
                    'expired_at' => now()->addMinutes((int) Configuration::getValue('topup_invoice_duration', 30))->format('Y-m-d H:i:s')
                ]);
                
                if ($tripayResult['success']) {
                    $paymentData['payment_provider'] = 'Tripay';
                    $paymentData['payment_account'] = $paymentMethod->name;
                    $paymentData['payment_code'] = $paymentMethod->method_code ?? null;
                    $paymentData['tripay_reference'] = $tripayResult['reference'];
                    $paymentData['tripay_payment_url'] = $tripayResult['payment_url'];
                    $paymentData['tripay_qr_code'] = $tripayResult['qr_code'];
                    $paymentData['tripay_merchant_ref'] = $tripayResult['merchant_ref'];
                    $paymentData['payment_notes'] = json_encode([
                        'provider' => 'Tripay',
                        'payment_method_id' => $paymentMethod->id,
                        'tripay_reference' => $tripayResult['reference'],
                        'created_at' => now()->format('Y-m-d H:i:s')
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal membuat transaksi Tripay: ' . $tripayResult['message']
                    ], 500);
                }
            } else {
                // Handle manual payment - use data from payment_methods table
                $paymentData['payment_provider'] = 'Manual';
                $paymentData['payment_account'] = $paymentMethod->method_code ?? 'No Account';
                $paymentData['payment_code'] = $paymentMethod->method_code ?? null;
                $paymentData['payment_notes'] = json_encode([
                    'provider' => 'Manual',
                    'payment_method_id' => $paymentMethod->id,
                    'created_at' => now()->format('Y-m-d H:i:s')
                ]);
            }
            
            // Create topup record
            $topup = Topup::create($paymentData);
            
            // Kirim notifikasi WhatsApp untuk topup baru
            if (FonnteHelper::isConfigured() && $member->phone) {
                try {
                    $topupData = [
                        'username' => $member->username,
                        'topup_id' => $topupId,
                        'amount' => $request->amount,
                        'payment_method' => $paymentMethod->name
                    ];
                    
                    FonnteHelper::sendTopupNewNotification($member->phone, $topupData);
                } catch (\Exception $e) {
                    Log::error('Failed to send topup new WhatsApp notification', [
                        'error' => $e->getMessage(),
                        'member_id' => $member->id,
                        'topup_id' => $topupId
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Topup berhasil diproses',
                'data' => [
                    'topup_id' => $topupId,
                    'amount' => $request->amount,
                    'payment_method' => $paymentMethod->name
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses topup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show topup history
     */
    public function showTopupHistory(Request $request)
    {
        $member = Auth::guard('member')->user();
        
        $query = Topup::where('member_id', $member->id);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('topup_id', 'like', "%{$search}%")
                  ->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }
        
        // Get entries per page
        $entries = $request->get('entries', 25);
        $topups = $query->orderBy('tanggal', 'desc')->paginate($entries);

        return view('member.riwayat-topup', compact('topups'));
    }

    /**
     * Show topup invoice
     */
    public function showInvoice($topupId)
    {
        $member = Auth::guard('member')->user();
        $topup = Topup::where('topup_id', $topupId)
                     ->where('member_id', $member->id)
                     ->first();

        if (!$topup) {
            return redirect()->route('member.topup.history')
                           ->with('error', 'Topup tidak ditemukan');
        }

        // Generate unique code (last 3 digits of topup ID)
        $uniqueCode = substr($topup->topup_id, -3);
        
        // Get invoice duration from configuration
        $invoiceDuration = Configuration::getValue('topup_invoice_duration', 30);
        
        // Get payment data based on provider
        if ($topup->payment_provider === 'Tripay') {
            // For Tripay, use stored data or fallback
            $paymentAccount = $topup->payment_account ?? 'Tripay Gateway';
        } else {
            // For manual payment, use stored data from payment_methods
            $paymentAccount = $topup->payment_account ?? 'No Account';
        }

        return view('member.invoice-topup', compact('topup', 'uniqueCode', 'paymentAccount', 'invoiceDuration'));
    }

    /**
     * Get topup status
     */
    public function getTopupStatus($topupId)
    {
        $member = Auth::guard('member')->user();
        $topup = Topup::where('topup_id', $topupId)
                     ->where('member_id', $member->id)
                     ->first();

        if (!$topup) {
            return response()->json([
                'success' => false,
                'message' => 'Topup tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'topup_id' => $topup->topup_id,
                'amount' => $topup->jumlah,
                'status' => $topup->status,
                'payment_method' => $topup->payment_method,
                'tanggal' => $topup->tanggal->format('Y-m-d H:i:s')
            ]
        ]);
    }

    /**
     * Check if payment method is balance payment (InnerPay)
     */
    private function isBalancePayment($paymentMethod)
    {
        return strtolower($paymentMethod->provider) === 'innerpay' || 
               strtolower($paymentMethod->name) === 'innerpay' ||
               strpos(strtolower($paymentMethod->name), 'innerpay') !== false ||
               strpos(strtolower($paymentMethod->name), 'balance') !== false;
    }

    /**
     * Check if payment method is Tripay
     */
    private function isTripayPayment($paymentMethod)
    {
        // Check if payment method provider is Tripay or QRIS
        $isTripay = strtolower($paymentMethod->provider) === 'tripay' || 
                   strtolower($paymentMethod->name) === 'tripay' ||
                   strpos(strtolower($paymentMethod->name), 'tripay') !== false ||
                   strtolower($paymentMethod->name) === 'qris' ||
                   strpos(strtolower($paymentMethod->name), 'qris') !== false;
        
        return $isTripay;
    }
}
