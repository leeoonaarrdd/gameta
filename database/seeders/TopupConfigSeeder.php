<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuration;

class TopupConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default topup configuration
        Configuration::setValue('topup_prefix', 'TOPUP', 'string');
        Configuration::setValue('topup_invoice_duration', '30', 'integer');
    }
}
