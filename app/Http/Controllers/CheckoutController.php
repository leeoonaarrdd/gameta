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
use App\Services\DigiflazzService;
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
            
            // Process to Digiflazz if product provider is Digiflazz
            $digiflazzResult = null;
            if ($purchase->product && $purchase->product->provider === 'Digiflazz') {
                $digiflazzResult = $this->processToDigiflazz($purchase);
                
                if ($digiflazzResult && isset($digiflazzResult['success']) && $digiflazzResult['success']) {
                    // Update purchase with Digiflazz data
                    $purchase->update([
                        'notes' => json_encode(array_merge(
                            json_decode($purchase->notes, true) ?? [],
                            [
                                'digiflazz_processed' => true,
                                'digiflazz_ref_id' => $digiflazzResult['ref_id'] ?? '',
                                'digiflazz_status' => $digiflazzResult['status'] ?? '',
                                'digiflazz_message' => $digiflazzResult['message'] ?? ''
                            ]
                        ))
                    ]);
                } else {
                    // Digiflazz failed but payment completed
                    $purchase->update([
                        'notes' => json_encode(array_merge(
                            json_decode($purchase->notes, true) ?? [],
                            [
                                'digiflazz_error' => $digiflazzResult && isset($digiflazzResult['message']) ? $digiflazzResult['message'] : 'Unknown error'
                            ]
                        ))
                    ]);
                }
            }
            
            // Kirim notifikasi WhatsApp jika Fonnte sudah dikonfigurasi
            if (FonnteHelper::isConfigured()) {
                try {
                    $orderNotificationData = [
                        'total_amount' => $orderData['total'],
                        'payment_method' => $paymentMethod->name
                    ];
                    
                    // Add Digiflazz info to notification if available
                    if ($digiflazzResult && isset($digiflazzResult['success']) && $digiflazzResult['success']) {
                        $orderNotificationData['digiflazz_ref_id'] = $digiflazzResult['ref_id'] ?? '';
                        $orderNotificationData['digiflazz_status'] = $digiflazzResult['status'] ?? '';
                    }
                    
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
            
            // Prepare response message
            $message = 'Pembayaran berhasil! Saldo telah dipotong.';
            if ($digiflazzResult && isset($digiflazzResult['success'])) {
                if ($digiflazzResult['success']) {
                    $message .= ' Produk berhasil diproses ke Digiflazz.';
                } else {
                    $message .= ' Pembayaran berhasil tapi gagal diproses ke Digiflazz: ' . ($digiflazzResult['message'] ?? 'Unknown error');
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect_url' => route('checkout.payment', ['order_id' => $orderData['order_id']]),
                'order_id' => $orderData['order_id'],
                'payment_completed' => true,
                'digiflazz_result' => $digiflazzResult
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

    /**
     * Process manual payment confirmation and send to Digiflazz
     */
    public function confirmManualPayment(Request $request, $order_id)
    {
        $request->validate([
            'status' => 'required|in:completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $purchase = Purchase::with(['product.game', 'member'])
            ->where('order_id', $order_id)
            ->first();

        // Debug logging for purchase loading
        Log::info('Debug: Purchase loaded for manual payment confirmation', [
            'order_id' => $order_id,
            'purchase_found' => $purchase ? 'YES' : 'NO',
            'purchase_id' => $purchase ? $purchase->id : 'NULL',
            'product_id' => $purchase ? $purchase->product_id : 'NULL',
            'product_loaded' => $purchase && $purchase->product ? 'YES' : 'NO',
            'product_name' => $purchase && $purchase->product ? $purchase->product->name : 'NULL',
            'sku' => $purchase && $purchase->product ? $purchase->product->sku : 'NULL'
        ]);

        if (!$purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        // Check if payment is already processed
        if ($purchase->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan sudah diproses'
            ], 400);
        }

        try {
            if ($request->status === 'completed') {
                // Update purchase status
                $purchase->update([
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'processed_at' => now(),
                    'notes' => json_encode(array_merge(
                        json_decode($purchase->notes, true) ?? [],
                        [
                            'manual_payment_confirmed_at' => now()->format('Y-m-d H:i:s'),
                            'admin_notes' => $request->notes
                        ]
                    ))
                ]);

                // Process to Digiflazz if product provider is Digiflazz
                if ($purchase->product && $purchase->product->provider === 'Digiflazz') {
                    $digiflazzResult = $this->processToDigiflazz($purchase);
                    
                    if ($digiflazzResult['success']) {
                        // Update purchase with Digiflazz data
                        $purchase->update([
                            'notes' => json_encode(array_merge(
                                json_decode($purchase->notes, true) ?? [],
                                [
                                    'digiflazz_processed' => true,
                                    'digiflazz_ref_id' => $digiflazzResult['ref_id'],
                                    'digiflazz_status' => $digiflazzResult['status'],
                                    'digiflazz_message' => $digiflazzResult['message']
                                ]
                            ))
                        ]);

                        // Send WhatsApp notification
                        $this->sendSuccessNotification($purchase, $digiflazzResult);

                        return response()->json([
                            'success' => true,
                            'message' => 'Pembayaran dikonfirmasi dan produk berhasil diproses ke Digiflazz',
                            'digiflazz_result' => $digiflazzResult
                        ]);
                    } else {
                        // Digiflazz failed but payment confirmed
                        $purchase->update([
                            'notes' => json_encode(array_merge(
                                json_decode($purchase->notes, true) ?? [],
                                [
                                    'digiflazz_error' => $digiflazzResult['message']
                                ]
                            ))
                        ]);

                        return response()->json([
                            'success' => false,
                            'message' => 'Pembayaran dikonfirmasi tapi gagal diproses ke Digiflazz: ' . $digiflazzResult['message'],
                            'digiflazz_result' => $digiflazzResult
                        ], 500);
                    }
                } else {
                    // Product is manual, no need to process to Digiflazz
                    $this->sendSuccessNotification($purchase);

                    return response()->json([
                        'success' => true,
                        'message' => 'Pembayaran dikonfirmasi dan produk diproses secara manual'
                    ]);
                }
            } else {
                // Cancel payment
                $purchase->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed',
                    'notes' => json_encode(array_merge(
                        json_decode($purchase->notes, true) ?? [],
                        [
                            'manual_payment_cancelled_at' => now()->format('Y-m-d H:i:s'),
                            'admin_notes' => $request->notes
                        ]
                    ))
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Pembayaran dibatalkan'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Manual payment confirmation failed', [
                'order_id' => $order_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses konfirmasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process purchase to Digiflazz
     */
    private function processToDigiflazz($purchase)
    {
        try {
            $digiflazzService = app(DigiflazzService::class);

            // Reload purchase with product to ensure fresh data
            $purchase = Purchase::with(['product.game', 'member'])
                ->find($purchase->id);

            // Get product details
            $product = $purchase->product;
            $notes = json_decode($purchase->notes, true) ?? [];

            // Debug logging
            Log::info('Debug: Purchase and Product details', [
                'purchase_id' => $purchase->id,
                'product_id' => $purchase->product_id,
                'product_loaded' => $product ? 'YES' : 'NO',
                'product_name' => $product ? $product->name : 'NULL',
                'sku' => $product ? $product->sku : 'NULL',
                'provider' => $product ? $product->provider : 'NULL'
            ]);

            // Validate product exists
            if (!$product) {
                Log::error('Product not found for purchase', [
                    'purchase_id' => $purchase->id,
                    'product_id' => $purchase->product_id
                ]);
                return [
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ];
            }

            // Validate SKU exists
            if (empty($product->sku)) {
                Log::error('SKU code is empty for product', [
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'all_product_fields' => $product->toArray()
                ]);
                return [
                    'success' => false,
                    'message' => 'SKU Code produk tidak ditemukan atau kosong'
                ];
            }

            // Get customer number from player fields
            $customerNo = $notes['player_fields'][0] ?? '08123456789'; // Default fallback
            
            // Special handling for Mobile Legends
            if ($product->game && strtolower($product->game->name) === 'mobile legends') {
                $userId = $notes['player_fields'][0] ?? '';
                $serverId = $notes['player_fields'][1] ?? '';
                
                if (!empty($userId) && !empty($serverId)) {
                    $customerNo = $userId . $serverId; // Combine User ID + Server ID
                    Log::info('Mobile Legends customer number format', [
                        'purchase_id' => $purchase->id,
                        'user_id' => $userId,
                        'server_id' => $serverId,
                        'combined_customer_no' => $customerNo
                    ]);
                } else {
                    Log::warning('Mobile Legends: Missing User ID or Server ID', [
                        'purchase_id' => $purchase->id,
                        'user_id' => $userId,
                        'server_id' => $serverId
                    ]);
                }
            }
            
            // Log player fields for debugging
            Log::info('Player fields debug', [
                'purchase_id' => $purchase->id,
                'player_fields' => $notes['player_fields'] ?? [],
                'customer_no_selected' => $customerNo,
                'game_name' => $product->game->name ?? 'Unknown'
            ]);
            
            // Validate customer number format (should be phone number for most games, but not Mobile Legends)
            if ($product->game && strtolower($product->game->name) !== 'mobile legends') {
                if (!preg_match('/^08[0-9]{8,11}$/', $customerNo)) {
                    Log::warning('Customer number format may be invalid', [
                        'purchase_id' => $purchase->id,
                        'customer_no' => $customerNo,
                        'game_name' => $product->game->name ?? 'Unknown',
                        'suggestion' => 'Customer number should be phone number format (08xxxxxxxxxx)'
                    ]);
                }
            }

            // Generate reference ID
            $refId = 'MANUAL_' . $purchase->order_id . '_' . time();

            // Log the request details
            Log::info('Processing Digiflazz topup for manual payment', [
                'purchase_id' => $purchase->id,
                'order_id' => $purchase->order_id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'customer_no' => $customerNo,
                'ref_id' => $refId
            ]);

            // Process to Digiflazz (production mode)
            $result = $digiflazzService->topUpProduction(
                $product->sku,
                $customerNo,
                $refId
            );

            if ($result && isset($result['data'])) {
                Log::info('Digiflazz topup successful', [
                    'purchase_id' => $purchase->id,
                    'ref_id' => $result['data']['ref_id'] ?? $refId,
                    'status' => $result['data']['status'] ?? 'pending'
                ]);

                return [
                    'success' => true,
                    'ref_id' => $result['data']['ref_id'] ?? $refId,
                    'status' => $result['data']['status'] ?? 'pending',
                    'message' => $result['data']['message'] ?? 'Success'
                ];
            } else {
                Log::error('Digiflazz topup failed', [
                    'purchase_id' => $purchase->id,
                    'result' => $result
                ]);

                return [
                    'success' => false,
                    'message' => 'Gagal memproses ke Digiflazz: ' . ($result['message'] ?? 'Unknown error')
                ];
            }
        } catch (\Exception $e) {
            Log::error('Digiflazz processing failed', [
                'purchase_id' => $purchase->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send success notification
     */
    private function sendSuccessNotification($purchase, $digiflazzResult = null)
    {
        if (!FonnteHelper::isConfigured()) {
            return;
        }

        try {
            $notes = json_decode($purchase->notes, true) ?? [];
            $whatsapp = $notes['whatsapp'] ?? null;

            if ($whatsapp) {
                $notificationData = [
                    'order_id' => $purchase->order_id,
                    'product_name' => $purchase->product->name ?? 'Unknown',
                    'total_amount' => $purchase->total_amount,
                    'status' => 'completed'
                ];

                if ($digiflazzResult) {
                    $notificationData['digiflazz_ref_id'] = $digiflazzResult['ref_id'];
                    $notificationData['digiflazz_status'] = $digiflazzResult['status'];
                }

                FonnteHelper::sendOrderNotification(
                    $purchase->order_id,
                    $whatsapp,
                    $notificationData
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to send success notification', [
                'error' => $e->getMessage(),
                'purchase_id' => $purchase->id
            ]);
        }
    }
}
