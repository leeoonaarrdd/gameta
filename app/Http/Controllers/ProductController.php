<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Game;
use App\Models\Icon;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['game', 'icon']);
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('provider', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhereHas('game', function($gameQuery) use ($request) {
                      $gameQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        // Get products with pagination
        $products = $query->orderBy('created_at', 'desc')->paginate($request->get('entries', 25));
        
        // If AJAX request, return JSON response
        if ($request->ajax()) {
            $html = view('admin.products.partials.table-rows', compact('products'))->render();
            $pagination = $products->appends($request->query())->links('vendor.pagination.tailwind')->toHtml();
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
        }
        
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $games = Game::active()->orderBy('name', 'asc')->get();
        $icons = Icon::active()->orderBy('name', 'asc')->get();
        
        return view('admin.products.create', compact('games', 'icons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'icon_id' => 'nullable|exists:icons,id',
            'price_tamu' => 'required|integer|min:0',
            'price_member' => 'required|integer|min:0',
            'original_price' => 'nullable|integer|min:0',
            'margin_tamu' => 'nullable|integer|min:0',
            'margin_member' => 'nullable|integer|min:0',
            'provider' => 'required|in:Manual,Digiflazz',
            'sku' => 'required|string|max:255|unique:products,sku',
            'is_active' => 'required|in:0,1',
            'auto_update_price' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Calculate margins if original price is provided
            $originalPrice = $request->original_price ?? 0;
            $marginTamu = $request->margin_tamu ?? ($request->price_tamu - $originalPrice);
            $marginMember = $request->margin_member ?? ($request->price_member - $originalPrice);

            Product::create([
                'name' => $request->name,
                'game_id' => $request->game_id,
                'icon_id' => $request->icon_id,
                'price_tamu' => $request->price_tamu,
                'price_member' => $request->price_member,
                'original_price' => $originalPrice,
                'margin_tamu' => $marginTamu,
                'margin_member' => $marginMember,
                'provider' => $request->provider,
                'sku' => $request->sku,
                'is_active' => $request->is_active,
                'auto_update_price' => $request->auto_update_price ?? false
            ]);

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product->load(['game', 'icon'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $games = Game::active()->orderBy('name', 'asc')->get();
        $icons = Icon::active()->orderBy('name', 'asc')->get();
        
        return view('admin.products.edit', compact('product', 'games', 'icons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'game_id' => 'required|exists:games,id',
            'icon_id' => 'nullable|exists:icons,id',
            'price_tamu' => 'required|integer|min:0',
            'price_member' => 'required|integer|min:0',
            'original_price' => 'nullable|integer|min:0',
            'margin_tamu' => 'nullable|integer|min:0',
            'margin_member' => 'nullable|integer|min:0',
            'provider' => 'required|in:Manual,Digiflazz',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'is_active' => 'required|in:0,1',
            'auto_update_price' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Calculate margins if original price is provided
            $originalPrice = $request->original_price ?? 0;
            $marginTamu = $request->margin_tamu ?? ($request->price_tamu - $originalPrice);
            $marginMember = $request->margin_member ?? ($request->price_member - $originalPrice);

            $product->update([
                'name' => $request->name,
                'game_id' => $request->game_id,
                'icon_id' => $request->icon_id,
                'price_tamu' => $request->price_tamu,
                'price_member' => $request->price_member,
                'original_price' => $originalPrice,
                'margin_tamu' => $marginTamu,
                'margin_member' => $marginMember,
                'provider' => $request->provider,
                'sku' => $request->sku,
                'is_active' => $request->is_active,
                'auto_update_price' => $request->auto_update_price ?? false
            ]);

            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate produk: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get products from Digiflazz
     */
    public function getFromDigiflazz(Request $request)
    {
        try {
            $digiflazzService = new DigiflazzService();
            
            if (!$digiflazzService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konfigurasi Digiflazz belum lengkap.'
                ], 400);
            }

            $prices = $digiflazzService->checkPrice();
            
            if (!$prices) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data dari Digiflazz.'
                ], 400);
            }

            // Handle different response structures from Digiflazz
            $productsData = [];
            if (isset($prices['data'])) {
                $productsData = $prices['data'];
            } elseif (isset($prices['pricelist'])) {
                $productsData = $prices['pricelist'];
            } elseif (is_array($prices)) {
                $productsData = $prices;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Format data dari Digiflazz tidak valid.'
                ], 400);
            }

            if (empty($productsData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data produk dari Digiflazz.'
                ], 400);
            }

            $processedCount = 0;
            $createdCount = 0;
            $updatedCount = 0;
            $errorCount = 0;

            foreach ($productsData as $item) {
                try {
                    // Handle different field names from Digiflazz
                    $sku = $item['buyer_sku_code'] ?? $item['sku'] ?? $item['code'] ?? null;
                    $productName = $item['product_name'] ?? $item['name'] ?? $item['title'] ?? null;
                    $price = $item['price'] ?? $item['harga'] ?? 0;
                    
                    // Skip if required fields are missing
                    if (empty($sku) || empty($productName)) {
                        continue;
                    }

                    // Check if product already exists
                    $existingProduct = Product::where('sku', $sku)->first();
                    
                    if ($existingProduct) {
                        // Update existing product with new price
                        $existingProduct->update([
                            'original_price' => $price,
                            'price_tamu' => $price + $existingProduct->margin_tamu,
                            'price_member' => $price + $existingProduct->margin_member,
                            'last_price_update' => now()
                        ]);
                        $updatedCount++;
                    } else {
                        // Try to find matching game based on product name
                        $gameId = $this->findMatchingGame($productName);
                        
                        // Skip if no game found
                        if ($gameId === null) {
                            Log::warning('No matching game found for product', [
                                'sku' => $sku,
                                'name' => $productName
                            ]);
                            $errorCount++;
                            continue;
                        }
                        
                        // Create new product
                        $product = new Product();
                        $product->name = $productName;
                        $product->sku = $sku;
                        $product->game_id = $gameId;
                        $product->provider = 'Digiflazz';
                        $product->original_price = $price;
                        $product->price_tamu = $price; // Default margin 0
                        $product->price_member = $price; // Default margin 0
                        $product->margin_tamu = 0;
                        $product->margin_member = 0;
                        $product->is_active = true; // Set active by default
                        $product->auto_update_price = true;
                        $product->save();
                        $createdCount++;
                    }
                    
                    $processedCount++;
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('Error processing Digiflazz product', [
                        'sku' => $sku ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $message = "Berhasil memproses {$processedCount} produk dari Digiflazz. ";
            $message .= "Dibuat: {$createdCount}, Diupdate: {$updatedCount}";
            
            if ($errorCount > 0) {
                $message .= ", Error: {$errorCount}";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'processed' => $processedCount,
                    'created' => $createdCount,
                    'updated' => $updatedCount,
                    'errors' => $errorCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get products from Digiflazz', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dari Digiflazz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product prices from Digiflazz
     */
    public function updatePrices(Request $request)
    {
        try {
            $digiflazzService = new DigiflazzService();
            
            // Check if Digiflazz is configured
            if (!$digiflazzService->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Konfigurasi Digiflazz belum lengkap.'
                ], 400);
            }

            // Get products that need price update
            $products = Product::where('provider', 'Digiflazz')
                              ->where('auto_update_price', true)
                              ->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada produk yang perlu diupdate.'
                ], 400);
            }

            // Get price list from Digiflazz
            $prices = $digiflazzService->checkPrice();
            
            if (!$prices) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data harga dari Digiflazz.'
                ], 400);
            }

            // Handle different response structures from Digiflazz
            $productsData = [];
            if (isset($prices['data'])) {
                $productsData = $prices['data'];
            } elseif (isset($prices['pricelist'])) {
                $productsData = $prices['pricelist'];
            } elseif (is_array($prices)) {
                $productsData = $prices;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Format data dari Digiflazz tidak valid.'
                ], 400);
            }

            if (empty($productsData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data produk dari Digiflazz.'
                ], 400);
            }

            // Create price mapping
            $priceMap = [];
            foreach ($productsData as $item) {
                $sku = $item['buyer_sku_code'] ?? $item['sku'] ?? $item['code'] ?? null;
                $price = $item['price'] ?? $item['harga'] ?? null;
                
                if ($sku && $price !== null) {
                    $priceMap[$sku] = $price;
                }
            }

            $updatedCount = 0;
            $errorCount = 0;

            foreach ($products as $product) {
                try {
                    if (isset($priceMap[$product->sku])) {
                        $newOriginalPrice = $priceMap[$product->sku];
                        
                        // Calculate new prices based on margins
                        $newPriceTamu = $newOriginalPrice + $product->margin_tamu;
                        $newPriceMember = $newOriginalPrice + $product->margin_member;
                        
                        // Update product
                        $product->update([
                            'original_price' => $newOriginalPrice,
                            'price_tamu' => $newPriceTamu,
                            'price_member' => $newPriceMember,
                            'last_price_update' => now()
                        ]);
                        
                        $updatedCount++;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('Error updating product price', [
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $message = "Berhasil update harga {$updatedCount} produk.";
            if ($errorCount > 0) {
                $message .= " Error: {$errorCount} produk.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'updated' => $updatedCount,
                    'errors' => $errorCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update product prices', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal update harga produk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find matching game based on product name
     */
    private function findMatchingGame($productName)
    {
        try {
            // Get all active games
            $games = Game::active()->get();
            
            // Try to match product name with game names
            foreach ($games as $game) {
                // Check if game name appears in product name (case insensitive)
                if (stripos($productName, $game->name) !== false) {
                    return $game->id;
                }
            }
            
            // If no match found, use the first available game
            $defaultGame = Game::active()->first();
            
            if ($defaultGame) {
                return $defaultGame->id;
            }
            
            // If no games exist at all, create a default game
            $defaultGame = Game::create([
                'name' => 'Default Game',
                'slug' => 'default-game-' . time(),
                'description' => 'Default game for imported products from Digiflazz',
                'is_active' => true
            ]);
            
            return $defaultGame->id;
            
        } catch (\Exception $e) {
            Log::error('Error in findMatchingGame', [
                'product_name' => $productName,
                'error' => $e->getMessage()
            ]);
            
            // Return null if everything fails
            return null;
        }
    }
}
