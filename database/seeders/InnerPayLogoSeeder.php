<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Storage;

class InnerPayLogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari metode pembayaran Innerpay
        $innerpayMethod = PaymentMethod::where('name', 'like', '%innerpay%')
            ->orWhere('name', 'like', '%InnerPay%')
            ->orWhere('provider', 'like', '%innerpay%')
            ->orWhere('provider', 'like', '%InnerPay%')
            ->first();

        if ($innerpayMethod) {
            // Copy gambar dari public/images ke storage
            $sourcePath = public_path('images/Innerpay.png');
            $destinationPath = 'payment-methods/innerpay-logo.png';
            
            if (file_exists($sourcePath)) {
                // Pastikan direktori exists
                $directory = dirname(storage_path('app/public/' . $destinationPath));
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Copy file
                copy($sourcePath, storage_path('app/public/' . $destinationPath));
                
                // Update database
                $innerpayMethod->update([
                    'image' => $destinationPath
                ]);
                
                $this->command->info('Logo Innerpay berhasil diupdate!');
            } else {
                $this->command->error('File Innerpay.png tidak ditemukan di public/images/');
            }
        } else {
            $this->command->error('Metode pembayaran Innerpay tidak ditemukan di database.');
        }
    }
}
