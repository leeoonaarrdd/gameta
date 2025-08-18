<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\DigiflazzService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateProductPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-prices {--force : Force update semua produk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update harga produk dari Digiflazz berdasarkan margin yang sudah diset';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai update harga produk...');

        $digiflazzService = new DigiflazzService();
        
        // Check if Digiflazz is configured
        if (!$digiflazzService->isConfigured()) {
            $this->error('Konfigurasi Digiflazz belum lengkap.');
            return 1;
        }

        // Get products that need price update
        $query = Product::where('provider', 'Digiflazz');
        
        if (!$this->option('force')) {
            $query->where('auto_update_price', true);
        }
        
        $products = $query->get();

        if ($products->isEmpty()) {
            $this->info('Tidak ada produk yang perlu diupdate.');
            return 0;
        }

        $this->info("Menemukan {$products->count()} produk untuk diupdate.");

        // Get price list from Digiflazz
        $this->info('Mengambil data harga dari Digiflazz...');
        $prices = $digiflazzService->checkPrice();
        
        if (!$prices || !isset($prices['data'])) {
            $this->error('Gagal mengambil data harga dari Digiflazz.');
            return 1;
        }

        // Create price mapping
        $priceMap = [];
        foreach ($prices['data'] as $item) {
            $priceMap[$item['buyer_sku_code']] = $item['price'];
        }

        $updatedCount = 0;
        $errorCount = 0;

        $progressBar = $this->output->createProgressBar($products->count());
        $progressBar->start();

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
                    
                    Log::info('Product price updated', [
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'old_original_price' => $product->getOriginal('original_price'),
                        'new_original_price' => $newOriginalPrice,
                        'old_price_tamu' => $product->getOriginal('price_tamu'),
                        'new_price_tamu' => $newPriceTamu,
                        'old_price_member' => $product->getOriginal('price_member'),
                        'new_price_member' => $newPriceMember
                    ]);
                } else {
                    Log::warning('Product SKU not found in Digiflazz price list', [
                        'sku' => $product->sku,
                        'name' => $product->name
                    ]);
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error('Error updating product price', [
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'error' => $e->getMessage()
                ]);
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("Update selesai!");
        $this->info("Berhasil diupdate: {$updatedCount}");
        $this->info("Error: {$errorCount}");

        return 0;
    }
}
