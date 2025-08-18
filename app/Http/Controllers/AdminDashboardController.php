<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
use App\Models\Purchase;
use App\Models\Game;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Cache dashboard data for 5 minutes
        $cacheKey = 'admin_dashboard_data';
        $cacheDuration = 300; // 5 minutes
        
        $dashboardData = Cache::remember($cacheKey, $cacheDuration, function () {
            // Statistik dasar dari database yang sudah ada
            $totalUsers = User::count();
            $totalPurchases = Purchase::count();
            $totalGames = Game::where('is_active', true)->count();
            $totalProducts = Product::where('is_active', true)->count();

            // Top 3 Member dengan saldo terbanyak
            $topMembers = Member::where('status', 'active')
                ->orderBy('balance', 'desc')
                ->limit(3)
                ->get(['username', 'balance']);

            // Top 3 Games populer berdasarkan jumlah pembelian
            $popularGames = Game::select('games.name')
                ->selectRaw('COUNT(purchases.id) as purchase_count')
                ->join('products', 'games.id', '=', 'products.game_id')
                ->join('purchases', 'products.id', '=', 'purchases.product_id')
                ->where('games.is_active', true)
                ->where('products.is_active', true)
                ->groupBy('games.id', 'games.name')
                ->having('purchase_count', '>', 0)
                ->orderBy('purchase_count', 'desc')
                ->limit(3)
                ->get();

            // Data grafik pembelian 7 hari terakhir
            $chartData = $this->getPurchaseChartData();

            return [
                'totalUsers' => $totalUsers,
                'totalPurchases' => $totalPurchases,
                'totalGames' => $totalGames,
                'totalProducts' => $totalProducts,
                'topMembers' => $topMembers,
                'popularGames' => $popularGames,
                'chartData' => $chartData
            ];
        });

        return view('admin.dashboard', $dashboardData);
    }

    private function getPurchaseChartData()
    {
        $days = [];
        $data = [];
        
        $dayNames = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa', 
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $englishDay = $date->format('l');
            $days[] = $dayNames[$englishDay] ?? $englishDay;
            
            $count = Purchase::whereDate('created_at', $date->format('Y-m-d'))->count();
            $data[] = $count;
        }

        return [
            'labels' => $days,
            'data' => $data
        ];
    }
}
