<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class UpdateProductsMarginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            // Calculate margins based on current prices
            $originalPrice = $product->original_price ?? 0;
            $marginTamu = $product->price_tamu - $originalPrice;
            $marginMember = $product->price_member - $originalPrice;
            
            $product->update([
                'original_price' => $originalPrice,
                'margin_tamu' => max(0, $marginTamu),
                'margin_member' => max(0, $marginMember),
                'auto_update_price' => false // Default to false for existing products
            ]);
        }
        
        $this->command->info('Berhasil mengupdate margin untuk ' . $products->count() . ' produk.');
    }
}
