<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Topup;
use Carbon\Carbon;

class TopupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topups = [
            [
                'username' => 'user001',
                'topup_id' => 'TOPUP001',
                'jumlah' => 50000,
                'status' => 'success',
                'tanggal' => Carbon::now()->subDays(5),
            ],
            [
                'username' => 'user002',
                'topup_id' => 'TOPUP002',
                'jumlah' => 100000,
                'status' => 'pending',
                'tanggal' => Carbon::now()->subDays(3),
            ],
            [
                'username' => 'user003',
                'topup_id' => 'TOPUP003',
                'jumlah' => 25000,
                'status' => 'success',
                'tanggal' => Carbon::now()->subDays(1),
            ],
            [
                'username' => 'user004',
                'topup_id' => 'TOPUP004',
                'jumlah' => 75000,
                'status' => 'failed',
                'tanggal' => Carbon::now()->subHours(12),
            ],
            [
                'username' => 'user005',
                'topup_id' => 'TOPUP005',
                'jumlah' => 150000,
                'status' => 'cancelled',
                'tanggal' => Carbon::now()->subHours(6),
            ],
        ];

        foreach ($topups as $topup) {
            Topup::create($topup);
        }
    }
}
