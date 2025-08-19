<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

// Search API Routes
Route::get('/api/search', [App\Http\Controllers\SearchController::class, 'search'])->name('api.search');

// Member Authentication Routes
Route::get('/login', [App\Http\Controllers\MemberAuthController::class, 'showLogin'])->name('member.login');
Route::post('/login', [App\Http\Controllers\MemberAuthController::class, 'login'])->name('member.login.post');
Route::get('/register', [App\Http\Controllers\MemberAuthController::class, 'showRegister'])->name('member.register');
Route::post('/register', [App\Http\Controllers\MemberAuthController::class, 'register'])->name('member.register.post');
Route::get('/auth', [App\Http\Controllers\MemberAuthController::class, 'showAuth'])->name('member.auth');
Route::post('/logout', [App\Http\Controllers\MemberAuthController::class, 'logout'])->name('member.logout');

// Member Reset Password Routes
Route::get('/reset-password', [App\Http\Controllers\MemberAuthController::class, 'showResetPassword'])->name('member.reset-password');
Route::post('/reset-password', [App\Http\Controllers\MemberAuthController::class, 'resetPassword'])->name('member.reset-password.post');
Route::get('/verify-otp', [App\Http\Controllers\MemberAuthController::class, 'showVerifyOTP'])->name('member.verify-otp');
Route::post('/verify-otp', [App\Http\Controllers\MemberAuthController::class, 'verifyOTP'])->name('member.verify-otp.post');

// Member Registration OTP Routes
Route::get('/verify-registration-otp', [App\Http\Controllers\MemberAuthController::class, 'showVerifyRegistrationOTP'])->name('member.verify-registration-otp');
Route::post('/verify-registration-otp', [App\Http\Controllers\MemberAuthController::class, 'verifyRegistrationOTP'])->name('member.verify-registration-otp.post');

// Member Dashboard Routes (Protected)
Route::middleware('member')->group(function () {
    Route::get('/member/dashboard', [App\Http\Controllers\MemberDashboardController::class, 'index'])->name('member.dashboard');
    
    Route::get('/topup-saldo', [App\Http\Controllers\MemberTopupController::class, 'showTopupSaldo'])->name('member.topup-saldo');
    Route::post('/api/topup/process', [App\Http\Controllers\MemberTopupController::class, 'processTopup'])->name('member.topup.process');
    Route::get('/riwayat-topup', [App\Http\Controllers\MemberTopupController::class, 'showTopupHistory'])->name('member.topup.history');
    Route::get('/invoice-topup/{topupId}', [App\Http\Controllers\MemberTopupController::class, 'showInvoice'])->name('member.topup.invoice');
    Route::get('/api/topup/status/{topupId}', [App\Http\Controllers\MemberTopupController::class, 'getTopupStatus'])->name('member.topup.status');
    
    // Member Purchase Routes
    Route::get('/member/purchases', [App\Http\Controllers\MemberPurchaseController::class, 'index'])->name('member.purchases.index');
    
    // Member Profile Routes
    Route::get('/member/pengaturan-akun', [App\Http\Controllers\MemberProfileController::class, 'showPengaturanAkun'])->name('member.pengaturan-akun');
    Route::put('/member/update-profile', [App\Http\Controllers\MemberProfileController::class, 'updateProfile'])->name('member.update-profile');
});

Route::get('/cek-pesanan', [App\Http\Controllers\CekPesananController::class, 'index'])->name('cek-pesanan');
Route::post('/api/cek-pesanan/search', [App\Http\Controllers\CekPesananController::class, 'search'])->name('cek-pesanan.search');

Route::get('/daftar-harga', [App\Http\Controllers\DaftarHargaController::class, 'index'])->name('daftar-harga');

Route::get('/syarat-ketentuan', function () {
    return view('syarat-ketentuan');
});

Route::get('/kebijakan-privasi', function () {
    return view('kebijakan-privasi');
});

Route::get('/pertanyaan-umum', [App\Http\Controllers\PertanyaanUmumController::class, 'index']);

Route::get('/bantuan', [App\Http\Controllers\BantuanController::class, 'index']);
Route::post('/bantuan/kirim', [App\Http\Controllers\BantuanController::class, 'kirimPesan'])->name('bantuan.kirim');

// Checkout Routes
Route::get('/checkout/{gameSlug}', [App\Http\Controllers\CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout/process', [App\Http\Controllers\CheckoutController::class, 'process'])->name('checkout.process');
Route::post('/checkout/check-nickname', [App\Http\Controllers\CheckoutController::class, 'checkNickname'])->name('checkout.check-nickname');
Route::get('/checkout/payment/{order_id}', [App\Http\Controllers\CheckoutController::class, 'showPayment'])->name('checkout.payment');
Route::post('/checkout/confirm-manual-payment/{order_id}', [App\Http\Controllers\CheckoutController::class, 'confirmManualPayment'])->name('checkout.confirm-manual-payment');
Route::post('/checkout/update-status', [App\Http\Controllers\CheckoutController::class, 'updatePurchaseStatus'])->name('checkout.update-status');
Route::get('/checkout/purchase/{orderId}', [App\Http\Controllers\CheckoutController::class, 'getPurchaseDetails'])->name('checkout.purchase-details');

// Tripay Routes
Route::post('/api/tripay/callback', [App\Http\Controllers\TripayController::class, 'callback'])->name('tripay.callback');
Route::post('/api/tripay/check-status', [App\Http\Controllers\TripayController::class, 'checkStatus'])->name('tripay.check-status');
Route::get('/api/tripay/payment-methods', [App\Http\Controllers\TripayController::class, 'getPaymentMethods'])->name('tripay.payment-methods');
Route::get('/api/tripay/test-connection', [App\Http\Controllers\TripayController::class, 'testConnection'])->name('tripay.test-connection');

// Digiflazz Routes
Route::post('/api/digiflazz/callback', [App\Http\Controllers\DigiflazzController::class, 'callback'])->name('digiflazz.callback');
Route::get('/api/digiflazz/test-connection', [App\Http\Controllers\DigiflazzController::class, 'testConnection'])->middleware('digiflazz.configured')->name('digiflazz.test-connection');
Route::get('/api/digiflazz/balance', [App\Http\Controllers\DigiflazzController::class, 'checkBalance'])->middleware('digiflazz.configured')->name('digiflazz.balance');
Route::get('/api/digiflazz/prices', [App\Http\Controllers\DigiflazzController::class, 'checkPrice'])->middleware('digiflazz.configured')->name('digiflazz.prices');
Route::get('/api/digiflazz/prices/{productCode}', [App\Http\Controllers\DigiflazzController::class, 'checkPrice'])->middleware('digiflazz.configured')->name('digiflazz.prices.product');
Route::post('/admin/products/get-from-digiflazz', [App\Http\Controllers\ProductController::class, 'getFromDigiflazz'])->name('admin.products.get-from-digiflazz');

  // Fonnte Routes
  Route::prefix('api/fonnte')->group(function () {
      Route::post('/send-message', [App\Http\Controllers\FonnteController::class, 'sendMessage'])->name('fonnte.send-message');
    Route::post('/send-media', [App\Http\Controllers\FonnteController::class, 'sendMedia'])->name('fonnte.send-media');
    Route::get('/check-message-status', [App\Http\Controllers\FonnteController::class, 'checkMessageStatus'])->name('fonnte.check-message-status');
    Route::get('/check-device-status', [App\Http\Controllers\FonnteController::class, 'checkDeviceStatus'])->name('fonnte.check-device-status');
    Route::post('/webhook', [App\Http\Controllers\FonnteController::class, 'webhook'])->name('fonnte.webhook');
    Route::post('/format-phone', [App\Http\Controllers\FonnteController::class, 'formatPhone'])->name('fonnte.format-phone');
    Route::post('/send-order-notification/{orderId}', [App\Http\Controllers\FonnteController::class, 'sendOrderNotification'])->name('fonnte.send-order-notification');
    Route::post('/send-payment-notification/{orderId}/{status}', [App\Http\Controllers\FonnteController::class, 'sendPaymentNotification'])->name('fonnte.send-payment-notification');
});




// Admin Routes
Route::prefix('admin')->group(function () {
    // Login Routes (tidak memerlukan middleware)
    Route::get('/login', function () {
        if (Auth::check()) {
            return redirect('/admin/dashboard');
        }
        return view('admin.login');
    })->name('admin.login');
    
    Route::post('/login', [App\Http\Controllers\AdminController::class, 'login'])->name('admin.login.post');
    
    // Protected Admin Routes
    Route::middleware('admin')->group(function () {
        Route::post('/logout', [App\Http\Controllers\AdminController::class, 'logout'])->name('admin.logout');
        
        Route::get('/', function () {
            return redirect('/admin/dashboard');
        });
        
        Route::get('/dashboard', [App\Http\Controllers\AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/categories', [App\Http\Controllers\CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'show'])->name('admin.categories.show');
    Route::put('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::get('/categories-list', [App\Http\Controllers\CategoryController::class, 'getCategories'])->name('admin.categories.list');
Route::post('/categories/update-order', [App\Http\Controllers\CategoryController::class, 'updateOrder'])->name('admin.categories.update-order');
    
    // Admins Routes
    Route::resource('admins', App\Http\Controllers\AdminController::class)->names([
        'index' => 'admin.admins.index',
        'create' => 'admin.admins.create',
        'store' => 'admin.admins.store',
        'show' => 'admin.admins.show',
        'edit' => 'admin.admins.edit',
        'update' => 'admin.admins.update',
        'destroy' => 'admin.admins.destroy',
    ]);
    
    // Members Routes
    Route::resource('members', App\Http\Controllers\MemberController::class)->names([
        'index' => 'admin.members.index',
        'show' => 'admin.members.show',
        'edit' => 'admin.members.edit',
        'update' => 'admin.members.update',
        'destroy' => 'admin.members.destroy',
    ]);
    Route::put('/members/{member}/toggle-status', [App\Http\Controllers\MemberController::class, 'toggleStatus'])->name('admin.members.toggle-status');
    
    // Targets Routes
    Route::resource('targets', App\Http\Controllers\TargetController::class)->names([
        'index' => 'admin.targets.index',
        'create' => 'admin.targets.create',
        'store' => 'admin.targets.store',
        'show' => 'admin.targets.show',
        'edit' => 'admin.targets.edit',
        'update' => 'admin.targets.update',
        'destroy' => 'admin.targets.destroy',
    ]);
    
    // Games Routes
    Route::resource('games', App\Http\Controllers\GameController::class)->names([
        'index' => 'admin.games.index',
        'create' => 'admin.games.create',
        'store' => 'admin.games.store',
        'show' => 'admin.games.show',
        'edit' => 'admin.games.edit',
        'update' => 'admin.games.update',
        'destroy' => 'admin.games.destroy',
    ]);
    Route::post('/games/update-order', [App\Http\Controllers\GameController::class, 'updateOrder'])->name('admin.games.update-order');
    
    // Products Routes
    Route::resource('products', App\Http\Controllers\ProductController::class)->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'show' => 'admin.products.show',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);
    Route::post('/products/update-prices', [App\Http\Controllers\ProductController::class, 'updatePrices'])->name('admin.products.update-prices');
    
    // Icons Routes
    Route::get('/icons', [App\Http\Controllers\IconController::class, 'index'])->name('admin.icons.index');
    Route::post('/icons', [App\Http\Controllers\IconController::class, 'store'])->name('admin.icons.store');
    Route::delete('/icons/{icon}', [App\Http\Controllers\IconController::class, 'destroy'])->name('admin.icons.destroy');
    
    // Banners Routes
    Route::get('/banners', [App\Http\Controllers\BannerController::class, 'index'])->name('admin.banners.index');
    Route::post('/banners', [App\Http\Controllers\BannerController::class, 'store'])->name('admin.banners.store');
    Route::delete('/banners/{filename}', [App\Http\Controllers\BannerController::class, 'destroy'])->name('admin.banners.destroy');
    
    // Configuration Routes
    Route::get('/configuration', [App\Http\Controllers\ConfigurationController::class, 'index'])->name('admin.configuration.index');
    Route::put('/configuration', [App\Http\Controllers\ConfigurationController::class, 'update'])->name('admin.configuration.update');
    Route::put('/configuration/tripay', [App\Http\Controllers\ConfigurationController::class, 'updateTripay'])->name('admin.configuration.update-tripay');
    Route::put('/configuration/digiflazz', [App\Http\Controllers\ConfigurationController::class, 'updateDigiflazz'])->name('admin.configuration.update-digiflazz');
    Route::put('/configuration/fonnte', [App\Http\Controllers\ConfigurationController::class, 'updateFonnte'])->name('admin.configuration.update-fonnte');
    
    // Content Routes
    Route::get('/content', [App\Http\Controllers\ContentController::class, 'index'])->name('admin.content.index');
    Route::put('/content', [App\Http\Controllers\ContentController::class, 'update'])->name('admin.content.update');
    
    // Bantuan Routes
    Route::get('/bantuan', function () {
        return view('admin.bantuan');
    })->name('admin.bantuan.index');
    Route::put('/bantuan', [App\Http\Controllers\ConfigurationController::class, 'updateBantuan'])->name('admin.bantuan.update');
    
    // Social Media Routes
    Route::resource('social-media', App\Http\Controllers\SocialMediaController::class)->names([
        'index' => 'admin.social-media.index',
        'create' => 'admin.social-media.create',
        'store' => 'admin.social-media.store',
        'show' => 'admin.social-media.show',
        'edit' => 'admin.social-media.edit',
        'update' => 'admin.social-media.update',
        'destroy' => 'admin.social-media.destroy',
    ]);
    
    // FAQ Routes
    Route::resource('faqs', App\Http\Controllers\FaqController::class)->names([
        'index' => 'admin.faqs.index',
        'create' => 'admin.faqs.create',
        'store' => 'admin.faqs.store',
        'edit' => 'admin.faqs.edit',
        'update' => 'admin.faqs.update',
        'destroy' => 'admin.faqs.destroy',
    ])->except(['show']);
    
    // FAQ Categories Routes
    Route::get('/faq-categories', [App\Http\Controllers\FaqCategoryController::class, 'index'])->name('admin.faq-categories.index');
    Route::post('/faq-categories', [App\Http\Controllers\FaqCategoryController::class, 'store'])->name('admin.faq-categories.store');
    Route::delete('/faq-categories/{id}', [App\Http\Controllers\FaqCategoryController::class, 'destroy'])->name('admin.faq-categories.destroy');
    
    // Payment Methods Routes
    Route::resource('payment-methods', App\Http\Controllers\PaymentMethodController::class)->names([
        'index' => 'admin.payment-methods.index',
        'create' => 'admin.payment-methods.create',
        'store' => 'admin.payment-methods.store',
        'show' => 'admin.payment-methods.show',
        'edit' => 'admin.payment-methods.edit',
        'update' => 'admin.payment-methods.update',
        'destroy' => 'admin.payment-methods.destroy',
    ]);
    
    // Payment Method Categories Routes
    Route::get('/payment-method-categories', [App\Http\Controllers\PaymentMethodCategoryController::class, 'index'])->name('admin.payment-method-categories.index');
    Route::post('/payment-method-categories', [App\Http\Controllers\PaymentMethodCategoryController::class, 'store'])->name('admin.payment-method-categories.store');
    Route::delete('/payment-method-categories/{paymentMethodCategory}', [App\Http\Controllers\PaymentMethodCategoryController::class, 'destroy'])->name('admin.payment-method-categories.destroy');
    
    // WhatsApp Routes
    Route::get('/whatsapp', [App\Http\Controllers\WhatsAppController::class, 'index'])->name('admin.whatsapp.index');
    Route::put('/whatsapp', [App\Http\Controllers\WhatsAppController::class, 'update'])->name('admin.whatsapp.update');
    
    
    // Purchases Routes
    Route::get('/purchases', [App\Http\Controllers\PurchaseController::class, 'index'])->name('admin.purchases.index');
    Route::get('/purchases/{purchase}', [App\Http\Controllers\PurchaseController::class, 'show'])->name('admin.purchases.show');
    Route::get('/purchases/{purchase}/edit', [App\Http\Controllers\PurchaseController::class, 'edit'])->name('admin.purchases.edit');
    Route::put('/purchases/{purchase}', [App\Http\Controllers\PurchaseController::class, 'update'])->name('admin.purchases.update');
    Route::put('/purchases/{purchase}/status', [App\Http\Controllers\PurchaseController::class, 'updateStatus'])->name('admin.purchases.update-status');
    Route::post('/purchases/config', [App\Http\Controllers\PurchaseController::class, 'updateConfig'])->name('admin.purchases.config');
    Route::delete('/purchases/{purchase}', [App\Http\Controllers\PurchaseController::class, 'destroy'])->name('admin.purchases.destroy');
    
    // Topups Routes
    Route::resource('topups', App\Http\Controllers\TopupController::class)->names([
        'index' => 'admin.topups.index',
        'create' => 'admin.topups.create',
        'store' => 'admin.topups.store',
        'show' => 'admin.topups.show',
        'edit' => 'admin.topups.edit',
        'update' => 'admin.topups.update',
        'destroy' => 'admin.topups.destroy',
    ]);
    Route::post('/topups/config', [App\Http\Controllers\TopupController::class, 'updateConfig'])->name('admin.topups.config');
    Route::post('/topups/{topup}/accept', [App\Http\Controllers\TopupController::class, 'acceptTopup'])->name('admin.topups.accept');
    
    // Digiflazz Test Routes (Integrated with Configuration)
    Route::get('/digiflazz-test/connection', function() {
        $digiflazzService = app(\App\Services\DigiflazzService::class);
        return response()->json($digiflazzService->testConnection());
    })->name('admin.digiflazz-test.connection');
    
    Route::get('/digiflazz-test/balance', function() {
        $digiflazzService = app(\App\Services\DigiflazzService::class);
        $balance = $digiflazzService->checkBalance();
        if ($balance && isset($balance['data'])) {
            return response()->json(['success' => true, 'message' => 'Saldo berhasil dicek', 'data' => $balance]);
        }
        return response()->json(['success' => false, 'message' => 'Gagal cek saldo', 'response' => $balance]);
    })->name('admin.digiflazz-test.balance');
    
    Route::get('/digiflazz-test/price-list', function() {
        $digiflazzService = app(\App\Services\DigiflazzService::class);
        $priceList = $digiflazzService->checkPrice();
        if ($priceList && isset($priceList['data'])) {
            return response()->json(['success' => true, 'message' => 'Price list berhasil diambil', 'data' => $priceList['data'], 'count' => count($priceList['data'])]);
        }
        return response()->json(['success' => false, 'message' => 'Gagal ambil price list', 'response' => $priceList]);
    })->name('admin.digiflazz-test.price-list');
    
    Route::post('/digiflazz-test/topup', function(\Illuminate\Http\Request $request) {
        $request->validate(['buyer_sku_code' => 'required|string', 'customer_no' => 'required|string']);
        $digiflazzService = app(\App\Services\DigiflazzService::class);
        $result = $digiflazzService->testTopUp($request->buyer_sku_code, $request->customer_no);
        return response()->json($result);
    })->name('admin.digiflazz-test.topup');
    
    Route::post('/digiflazz-test/status', function(\Illuminate\Http\Request $request) {
        $request->validate(['ref_id' => 'required|string']);
        $digiflazzService = app(\App\Services\DigiflazzService::class);
        $result = $digiflazzService->testTransactionStatus($request->ref_id);
        return response()->json($result);
    })->name('admin.digiflazz-test.status');
    
    Route::get('/digiflazz-test/run-all', function() {
        $digiflazzService = app(\App\Services\DigiflazzService::class);
        $results = $digiflazzService->runTopUpTests();
        return response()->json(['success' => true, 'message' => 'Semua test selesai dijalankan', 'results' => $results]);
    })->name('admin.digiflazz-test.run-all');
    
    Route::get('/digiflazz-test/configuration', function() {
        $digiflazzService = app(\App\Services\DigiflazzService::class);
        $isConfigured = $digiflazzService->isConfigured();
        $isWebhookConfigured = $digiflazzService->isWebhookConfigured();
        return response()->json([
            'success' => true,
            'data' => [
                'is_configured' => $isConfigured,
                'is_webhook_configured' => $isWebhookConfigured,
                'message' => $isConfigured ? 'Digiflazz sudah dikonfigurasi' : 'Digiflazz belum dikonfigurasi'
            ]
        ]);
    })->name('admin.digiflazz-test.configuration');
    
    Route::get('/digiflazz-test/debug-signature', function() {
        $digiflazzService = app(\App\Services\DigiflazzService::class);
        $debug = $digiflazzService->debugSignature();
        return response()->json([
            'success' => true,
            'data' => $debug
        ]);
    })->name('admin.digiflazz-test.debug-signature');
    });
});
