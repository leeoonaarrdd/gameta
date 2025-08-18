<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodCategory;
use App\Models\Purchase;
use App\Models\Configuration;

use App\Models\Member;
use App\Services\RapidApiService;
use App\Services\TripayService;
use App\Helpers\FonnteHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;


class CheckoutController extends Controller
{
    protected $rapidApiService;
    protected $tripayService;

    public function __construct(RapidApiService $rapidApiService, TripayService $tripayService)
    {
        $this->rapidApiService = $rapidApiService;
        $this->tripayService = $tripayService;
    }

    public function show($gameSlug)
    {
        $game = Game::with(['products.icon', 'target'])
            ->where('slug', $gameSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = $game->products()
            ->with('icon')
            ->where('is_active', true)
            ->orderBy('price_tamu', 'asc')
            ->get();

        $paymentMethods = PaymentMethod::with('category')
            ->where('is_active', true)
            ->orderBy('kategori', 'asc')
            ->orderBy('name', 'asc')
            ->get()
            ->groupBy('kategori');

        $paymentCategories = PaymentMethodCategory::all();

        return view('checkout', compact('game', 'products', 'paymentMethods', 'paymentCategories'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'whatsapp' => 'required|string|max:255',
            'player_fields' => 'nullable|string',
            'option_fields' => 'nullable|string',
        ]);

        // Decode JSON data
        $playerFields = json_decode($request->player_fields, true) ?? [];
        $optionFields = json_decode($request->option_fields, true) ?? [];

        // Get product and game info
        $product = Product::with('icon')->findOrFail($request->product_id);
        $game = $product->game;

        // Get payment method details
        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // Validate player fields based on target configuration
        if ($game->target && $game->target->input_fields) {
            $requiredFields = $game->target->input_fields;
            if (count($playerFields) < count($requiredFields)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mohon lengkapi semua field yang diperlukan'
                ], 422);
            }
        }

        // Validate option fields based on target configuration
        if ($game->target && $game->target->option_fields) {
            $requiredOptions = $game->target->option_fields;
            if (count($optionFields) < count($requiredOptions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mohon lengkapi semua opsi yang diperlukan'
                ], 422);
            }
        }

        // Determine payment method type based on provider and kategori
        $paymentMethodType = $this->determinePaymentMethodType($paymentMethod);

        // Generate order ID based on configuration
        $orderId = $this->generateOrderId();
        
        // Determine price based on member login status
        $isMemberLoggedIn = auth()->guard('member')->check();
        $productPrice = $isMemberLoggedIn ? $product->price_member : $product->price_tamu;
        
        // Calculate fees
        $adminFee = $this->calculateAdminFee($productPrice, $paymentMethod);
        $uniqueCode = $paymentMethod->has_unique_code ? rand(1, 999) : 0;
        $totalAmount = $productPrice + $adminFee + $uniqueCode;
        
        // Get invoice duration from configuration
        $invoiceDuration = (int) Configuration::getValue('invoice_duration', 30);
        $expiredAt = now()->addMinutes($invoiceDuration);

        // Create purchase record
        $purchaseData = [
            'order_id' => $orderId,
            'user_id' => null, // Tidak perlu user_id untuk pembelian
            'product_id' => $product->id,
            'quantity' => 1, // Default quantity
            'price' => $productPrice,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_method' => $paymentMethod->name,
            'payment_status' => 'pending',
            'notes' => json_encode([
                'player_fields' => $playerFields,
                'option_fields' => $optionFields,
                'player_nickname' => $this->getPlayerNickname($game, $playerFields),
                'player_id' => $playerFields[0] ?? 'Unknown',
                'admin_fee' => $adminFee,
                'unique_code' => $uniqueCode,
                'expired_at' => $expiredAt->format('Y-m-d H:i:s'),
                'is_member_price' => $isMemberLoggedIn,
                'whatsapp' => $request->whatsapp
            ])
        ];

        // Add member_id if member is logged in
        if ($isMemberLoggedIn) {
            $member = auth()->guard('member')->user();
            $purchaseData['member_id'] = $member->id;
        }

        $purchase = Purchase::create($purchaseData);

        // Create order data for session (for backward compatibility)
        $orderData = [
            'order_id' => $orderId,
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'game_id' => $game->id,
            'game_name' => $game->name,
            'product_name' => $product->name,
            'game' => [
                'name' => $game->name,
                'gambar' => $game->gambar
            ],
            'price' => $productPrice,
            'admin_fee' => $adminFee,
            'unique_code' => $uniqueCode,
            'total' => $totalAmount,
            'payment_method_id' => $request->payment_method_id,
            'payment_method' => $paymentMethod->name,
            'payment_method_code' => $paymentMethod->method_code,
            'payment_method_type' => $paymentMethodType,
            'payment_method_image' => $paymentMethod->image,
            'payment_method_provider' => $paymentMethod->provider,
            'payment_method_category' => $paymentMethod->kategori,
            'whatsapp' => $request->whatsapp,
            'player_fields' => $playerFields,
            'option_fields' => $optionFields,
            'player_nickname' => $this->getPlayerNickname($game, $playerFields),
            'player_id' => $playerFields[0] ?? 'Unknown',
            'created_at' => now()->format('Y-m-d H:i:s'),
            'expired_at' => $expiredAt->format('Y-m-d H:i:s'),
            'qr_code' => null, // Will be generated by Tripay
            'payment_url' => null, // Will be generated by Tripay
            'merchant_ref' => null, // Will be generated by Tripay
        ];

        // Store order in session for payment page
        session(['pending_order' => $orderData]);
        
        // Check if payment method is InnerPay (Balance)
        if ($this->isInnerPayPayment($paymentMethod)) {
            // Check if member is logged in
            if (!$isMemberLoggedIn) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran dengan saldo hanya tersedia untuk member yang sudah login'
                ], 422);
            }

            // Get member data
            $member = auth()->guard('member')->user();
            
            // Check if member has sufficient balance
            if ($member->balance < $totalAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo tidak mencukupi. Saldo Anda: Rp ' . number_format($member->balance, 0, ',', '.') . ', Total pembayaran: Rp ' . number_format($totalAmount, 0, ',', '.')
                ], 422);
            }

            // Deduct balance from member
            Member::where('id', $member->id)->decrement('balance', $totalAmount);

            // Update purchase status to completed
            $purchase->update([
                'status' => 'completed',
                'payment_status' => 'paid',
                'processed_at' => now(),
                'notes' => json_encode(array_merge(
                    json_decode($purchase->notes, true) ?? [],
                    [
                        'payment_processed_at' => now()->format('Y-m-d H:i:s'),
                        'member_id' => $member->id,
                        'balance_deducted' => $totalAmount,
                        'balance_remaining' => $member->balance
                    ]
                ))
            ]);

            // Update order data
            $orderData['status'] = 'completed';
            $orderData['payment_status'] = 'paid';
            $orderData['processed_at'] = now()->format('Y-m-d H:i:s');
            
            session(['pending_order' => $orderData]);
            
            // Kirim notifikasi WhatsApp jika Fonnte sudah dikonfigurasi
            if (FonnteHelper::isConfigured()) {
                try {
                    $orderNotificationData = [
                        'total_amount' => $orderData['total'],
                        'payment_method' => $paymentMethod->name
                    ];
                    
                    FonnteHelper::sendOrderNotification(
                        $orderData['order_id'],
                        $request->whatsapp,
                        $orderNotificationData
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send Fonnte notification', [
                        'error' => $e->getMessage(),
                        'order_id' => $orderData['order_id']
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil! Saldo telah dipotong.',
                'redirect_url' => route('checkout.payment', ['order_id' => $orderData['order_id']]),
                'order_id' => $orderData['order_id'],
                'payment_completed' => true
            ]);
        }

        // Check if payment method is Tripay
        if ($this->isTripayPayment($paymentMethod)) {
            // Create Tripay transaction
            $tripayResult = $this->tripayService->createTransaction($orderData);
            
            if ($tripayResult['success']) {
                // Update purchase with Tripay data
                $purchase->update([
                    'tripay_reference' => $tripayResult['reference'],
                    'tripay_payment_url' => $tripayResult['payment_url'],
                    'tripay_qr_code' => $tripayResult['qr_code'],
                    'tripay_merchant_ref' => $tripayResult['merchant_ref'],
                    'notes' => json_encode(array_merge(
                        json_decode($purchase->notes, true) ?? [],
                        [
                            'tripay_reference' => $tripayResult['reference'],
                            'tripay_payment_url' => $tripayResult['payment_url'],
                            'tripay_qr_code' => $tripayResult['qr_code'],
                            'tripay_merchant_ref' => $tripayResult['merchant_ref']
                        ]
                    ))
                ]);
                
                // Update order data with Tripay info
                $orderData['qr_code'] = $tripayResult['qr_code'];
                $orderData['payment_url'] = $tripayResult['payment_url'];
                $orderData['merchant_ref'] = $tripayResult['merchant_ref'];
                $orderData['tripay_reference'] = $tripayResult['reference'];
                
                session(['pending_order' => $orderData]);
                
                // Kirim notifikasi WhatsApp jika Fonnte sudah dikonfigurasi
                if (FonnteHelper::isConfigured()) {
                    try {
                        $orderNotificationData = [
                            'total_amount' => $orderData['total'],
                            'payment_method' => $paymentMethod->name
                        ];
                        
                        FonnteHelper::sendOrderNotification(
                            $orderData['order_id'],
                            $request->whatsapp,
                            $orderNotificationData
                        );
                    } catch (\Exception $e) {
                        Log::error('Failed to send Fonnte notification', [
                            'error' => $e->getMessage(),
                            'order_id' => $orderData['order_id']
                        ]);
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibuat!',
                    'redirect_url' => route('checkout.payment', ['order_id' => $orderData['order_id']]),
                    'order_id' => $orderData['order_id'],
                    'payment_url' => $tripayResult['payment_url'],
                    'qr_code' => $tripayResult['qr_code']
                ]);
            } else {
                // If Tripay fails, return error
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat transaksi pembayaran: ' . $tripayResult['message']
                ], 500);
            }
        }

        // Kirim notifikasi WhatsApp jika Fonnte sudah dikonfigurasi
        if (FonnteHelper::isConfigured()) {
            try {
                $orderNotificationData = [
                    'total_amount' => $orderData['total'],
                    'payment_method' => $paymentMethod->name
                ];
                
                FonnteHelper::sendOrderNotification(
                    $orderData['order_id'],
                    $request->whatsapp,
                    $orderNotificationData
                );
            } catch (\Exception $e) {
                Log::error('Failed to send Fonnte notification', [
                    'error' => $e->getMessage(),
                    'order_id' => $orderData['order_id']
                ]);
            }
        }
        
        // Return success with redirect to payment page for non-Tripay payments
        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibuat!',
            'redirect_url' => route('checkout.payment', ['order_id' => $orderData['order_id']]),
            'order_id' => $orderData['order_id']
        ]);
    }

    /**
     * Show payment confirmation page
     */
    public function showPayment(Request $request, $order_id = null)
    {
        // Use order_id from URL parameter if provided
        if ($order_id) {
            $purchase = Purchase::with(['member', 'product.game', 'product.icon'])->where('order_id', $order_id)->first();
            
            if (!$purchase) {
                return redirect('/')->with('error', 'Order ID tidak ditemukan');
            }
            
            // Convert purchase data to order format
            $order = $this->convertPurchaseToOrder($purchase);
        } else {
            // Use session data (existing flow for backward compatibility)
            $order = session('pending_order');
            
            if (!$order) {
                return redirect('/')->with('error', 'Tidak ada pesanan yang pending');
            }

            // Fallback: Load game data if not available in order
            if (!isset($order['game']) && isset($order['game_id'])) {
                $game = Game::find($order['game_id']);
                if ($game) {
                    $order['game'] = [
                        'name' => $game->name,
                        'gambar' => $game->gambar
                    ];
                }
            }

            // Fallback: Ensure player_fields is available
            if (!isset($order['player_fields']) && isset($order['player_id'])) {
                $order['player_fields'] = [$order['player_id']];
            }

            // Fallback: Load payment method data if not available in order
            if (!isset($order['payment_method_image']) && isset($order['payment_method_id'])) {
                $paymentMethod = PaymentMethod::find($order['payment_method_id']);
                if ($paymentMethod) {
                    $order['payment_method_image'] = $paymentMethod->image;
                    $order['payment_method'] = $paymentMethod->name;
                    $order['payment_method_code'] = $paymentMethod->method_code;
                    $order['payment_method_provider'] = $paymentMethod->provider;
                    $order['payment_method_category'] = $paymentMethod->kategori;
                }
            }
        }

        return view('payment-confirmation', compact('order'));
    }

    /**
     * Convert Purchase model to order array format
     */
    private function convertPurchaseToOrder($purchase)
    {
        $notes = json_decode($purchase->notes, true) ?? [];
        
        $order = [
            'order_id' => $purchase->order_id,
            'price' => $purchase->price,
            'total' => $purchase->total_amount,
            'admin_fee' => $notes['admin_fee'] ?? 0,
            'unique_code' => $notes['unique_code'] ?? 0,
            'created_at' => $purchase->created_at->format('Y-m-d H:i:s'),
            'expired_at' => $notes['expired_at'] ?? null,
            'status' => $purchase->status,
            'payment_status' => $purchase->payment_status,
            'payment_method' => $purchase->payment_method,
            'whatsapp' => $notes['whatsapp'] ?? '',
            'player_fields' => $notes['player_fields'] ?? [],
            'option_fields' => $notes['option_fields'] ?? [],
            'player_nickname' => $notes['player_nickname'] ?? 'Unknown',
            'player_id' => $notes['player_id'] ?? 'Unknown',
            'game_name' => $purchase->product->game->name ?? 'Unknown Game',
            'product_name' => $purchase->product->name ?? 'Unknown Product',
            'quantity' => $purchase->quantity,
            'qr_code' => $purchase->tripay_qr_code,
            'payment_url' => $purchase->tripay_payment_url,
            'merchant_ref' => $purchase->tripay_merchant_ref,
            'tripay_reference' => $purchase->tripay_reference,
        ];

        // Add game data
        if ($purchase->product && $purchase->product->game) {
            $order['game'] = [
                'name' => $purchase->product->game->name,
                'gambar' => $purchase->product->game->gambar
            ];
        }

        // Add payment method data
        if ($purchase->payment_method) {
            $paymentMethod = PaymentMethod::where('name', $purchase->payment_method)->first();
            if ($paymentMethod) {
                $order['payment_method_image'] = $paymentMethod->image;
                $order['payment_method_provider'] = $paymentMethod->provider;
                $order['payment_method_category'] = $paymentMethod->kategori;
                $order['payment_method_code'] = $paymentMethod->method_code;
            }
        }

        return $order;
    }

    /**
     * Get player nickname using RapidAPI
     */
    private function getPlayerNickname($game, $playerFields)
    {
        // Temporarily disabled to save RapidAPI limit during testing
        // Uncomment the code below when ready to use RapidAPI
        
        /*
        try {
            if (empty($playerFields)) {
                return 'Unknown';
            }

            $result = $this->rapidApiService->checkGameNickname($game->name, $playerFields);
            
            if ($result['success'] && $result['nickname']) {
                return $result['nickname'];
            }
            
            return 'Tidak ditemukan';
        } catch (\Exception $e) {
            return 'Gagal dicek';
        }
        */
        
        // Return first player field as nickname for testing
        if (!empty($playerFields)) {
            return $playerFields[0] ?? 'Unknown';
        }
        
        return 'Unknown';
    }

    /**
     * Check nickname for a game player based on player fields using RapidAPI
     */
    public function checkNickname(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'player_fields' => 'required|array',
            'player_fields.*' => 'required|string',
        ]);

        $game = Game::findOrFail($request->game_id);
        $playerFields = $request->player_fields;

        // Temporarily disabled to save RapidAPI limit during testing
        // Uncomment the code below when ready to use RapidAPI
        
        /*
        try {
            // Use RapidAPI service to check nickname
            $result = $this->rapidApiService->checkGameNickname($game->name, $playerFields);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'nickname' => null,
                'message' => 'Gagal mengecek nickname: ' . $e->getMessage()
            ], 500);
        }
        */
        
        // Return mock response for testing
        return response()->json([
            'success' => true,
            'nickname' => $playerFields[0] ?? 'Unknown',
            'message' => 'Nickname checked successfully (Testing Mode)'
        ]);
    }

    /**
     * Determine payment method type based on provider and category
     */
    private function determinePaymentMethodType($paymentMethod)
    {
        // If provider is Tripay, use category to determine type
        if ($paymentMethod->provider === 'Tripay') {
            $category = strtolower($paymentMethod->kategori);
            
            if (str_contains($category, 'qris') || str_contains($category, 'qr')) {
                return 'qris';
            } elseif (str_contains($category, 'bank') || str_contains($category, 'transfer')) {
                return 'bank_transfer';
            } elseif (str_contains($category, 'wallet') || str_contains($category, 'ewallet')) {
                return 'ewallet';
            } elseif (str_contains($category, 'convenience') || str_contains($category, 'indomaret') || str_contains($category, 'alfamart')) {
                return 'convenience_store';
            } else {
                return 'online_payment';
            }
        }
        
        // If provider is Manual, determine based on category
        if ($paymentMethod->provider === 'Manual') {
            $category = strtolower($paymentMethod->kategori);
            
            if (str_contains($category, 'bank') || str_contains($category, 'transfer')) {
                return 'bank_transfer';
            } elseif (str_contains($category, 'wallet') || str_contains($category, 'ewallet')) {
                return 'ewallet';
            } else {
                return 'manual_payment';
            }
        }
        
        return 'unknown';
    }

    /**
     * Calculate admin fee based on payment method configuration
     */
    private function calculateAdminFee($price, $paymentMethod)
    {
        $adminFee = 0;
        
        // Add fixed admin fee if set
        if ($paymentMethod->admin_fee) {
            $adminFee += $paymentMethod->admin_fee;
        }
        
        // Add percentage admin fee if set
        if ($paymentMethod->admin_fee_percentage) {
            $adminFee += ceil($price * ($paymentMethod->admin_fee_percentage / 100));
        }
        
        // Default 2% if no admin fee configured
        if ($adminFee == 0) {
            $adminFee = ceil($price * 0.02);
        }
        
        return $adminFee;
    }

    /**
     * Generate order ID based on configuration
     */
    private function generateOrderId()
    {
        $prefix = Configuration::getValue('order_prefix', 'ORD');
        $date = date('Ymd');
        
        // Get the last order number for today
        $lastOrder = Purchase::where('order_id', 'like', $prefix . $date . '%')
            ->orderBy('order_id', 'desc')
            ->first();
        
        if ($lastOrder) {
            // Extract the sequence number from the last order
            $lastSequence = (int) substr($lastOrder->order_id, -3);
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . $date . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }



    /**
     * Check if payment method is InnerPay (Balance)
     */
    private function isInnerPayPayment($paymentMethod)
    {
        return strtolower($paymentMethod->provider) === 'innerpay' || 
               strtolower($paymentMethod->name) === 'innerpay' ||
               strpos(strtolower($paymentMethod->name), 'innerpay') !== false;
    }

    /**
     * Check if payment method is Tripay
     */
    private function isTripayPayment($paymentMethod)
    {
        // Check if Tripay is configured
        if (!$this->tripayService->isConfigured()) {
            Log::warning('Tripay Payment: Payment gateway tidak dikonfigurasi');
            return false;
        }

        // Check if payment method provider is Tripay
        $isTripay = strtolower($paymentMethod->provider) === 'tripay' || 
                   strtolower($paymentMethod->name) === 'tripay' ||
                   strpos(strtolower($paymentMethod->name), 'tripay') !== false;


        
        return $isTripay;
    }

    /**
     * Update purchase status when payment is completed
     */
    public function updatePurchaseStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'status' => 'required|in:pending,processing,completed,cancelled,failed',
            'payment_status' => 'required|in:pending,paid,failed,expired'
        ]);

        $purchase = Purchase::where('order_id', $request->order_id)->first();
        
        if (!$purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase not found'
            ], 404);
        }

        $purchase->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'processed_at' => $request->status === 'completed' ? now() : null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Purchase status updated successfully'
        ]);
    }

    /**
     * Get purchase details by order ID
     */
    public function getPurchaseDetails($orderId)
    {
        $purchase = Purchase::with(['member', 'product.game'])
            ->where('order_id', $orderId)
            ->first();
        
        if (!$purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $purchase
        ]);
    }
}
