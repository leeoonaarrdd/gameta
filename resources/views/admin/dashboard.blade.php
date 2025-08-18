@extends('admin.layouts.app')

@section('title', 'Dashboard - Admin Panel')

@section('page-title', 'Dashboard')
@section('page-description', 'Selamat datang di panel admin Gameta')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 border border-gray-700/30 rounded-lg p-8 animate-on-scroll-bounce">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white mb-2 animate-on-scroll">Selamat Datang, Admin! 👋</h1>
                                 <p class="text-gray-400 text-lg animate-on-scroll animate-on-scroll-delay-1">Kelola dan pantau semua aktivitas dari dashboard ini.</p>
            </div>
            <div class="hidden lg:block animate-on-scroll-scale">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-purple-400 rounded-lg blur-lg opacity-20"></div>
                    <div class="relative w-24 h-24 bg-gradient-to-r from-purple-500 to-purple-400 rounded-lg flex items-center justify-center">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 border border-gray-700/30 rounded-lg p-6 animate-on-scroll animate-on-scroll-delay-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Pengguna</p>
                    <p class="text-3xl font-bold text-white">{{ $totalUsers }}</p>
                </div>
                                 <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-400 rounded-lg flex items-center justify-center animate-on-scroll-rotate">
                     <i class="fas fa-user-shield text-white text-xl"></i>
                 </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 border border-gray-700/30 rounded-lg p-6 animate-on-scroll animate-on-scroll-delay-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Pembelian</p>
                    <p class="text-3xl font-bold text-white">{{ $totalPurchases }}</p>
                </div>
                                 <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-400 rounded-lg flex items-center justify-center animate-on-scroll-rotate">
                     <i class="fas fa-shopping-cart text-white text-xl"></i>
                 </div>
            </div>
        </div>

        <!-- Total Games -->
        <div class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 border border-gray-700/30 rounded-lg p-6 animate-on-scroll animate-on-scroll-delay-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Games</p>
                    <p class="text-3xl font-bold text-white">{{ $totalGames }}</p>
                </div>
                                 <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-400 rounded-lg flex items-center justify-center animate-on-scroll-rotate">
                     <i class="fas fa-dice text-white text-xl"></i>
                 </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 border border-gray-700/30 rounded-lg p-6 animate-on-scroll animate-on-scroll-delay-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm font-medium">Produk</p>
                    <p class="text-3xl font-bold text-white">{{ $totalProducts }}</p>
                </div>
                                 <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-orange-400 rounded-lg flex items-center justify-center animate-on-scroll-rotate">
                     <i class="fas fa-box text-white text-xl"></i>
                 </div>
            </div>
        </div>
    </div>

         <!-- Recent Activity & Quick Actions -->
     <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Top 3 Saldo Terbanyak -->
          <div class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 border border-gray-700/30 rounded-lg p-6 animate-on-scroll-scale">
             <div class="flex items-center justify-between mb-6">
                 <h3 class="text-lg font-semibold text-white animate-on-scroll">Top 3 Saldo Terbanyak</h3>
             </div>
             <div class="overflow-x-auto">
                 <table class="w-full bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30">
                     <thead>
                         <tr class="border-b border-gray-700/30 animate-on-scroll-slide-up">
                             <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-1">Peringkat</th>
                             <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-2">Username</th>
                             <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-3">Saldo</th>
                         </tr>
                     </thead>
                     <tbody class="divide-y divide-gray-700/30">
                         @forelse($topMembers as $index => $member)
                         <tr class="hover:bg-gray-700/20 transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-{{ min($index + 1, 4) }}">
                             <td class="px-6 py-4">
                                 <div class="flex items-center">
                                     @if($index == 0)
                                         <div class="w-8 h-8 bg-gradient-to-r from-yellow-500 to-yellow-400 rounded-full flex items-center justify-center">
                                             <span class="text-white font-bold text-sm">{{ $index + 1 }}</span>
                                         </div>
                                     @elseif($index == 1)
                                         <div class="w-8 h-8 bg-gradient-to-r from-gray-400 to-gray-300 rounded-full flex items-center justify-center">
                                             <span class="text-white font-bold text-sm">{{ $index + 1 }}</span>
                                         </div>
                                     @else
                                         <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-orange-400 rounded-full flex items-center justify-center">
                                             <span class="text-white font-bold text-sm">{{ $index + 1 }}</span>
                                         </div>
                                     @endif
                                 </div>
                             </td>
                             <td class="px-6 py-4">
                                 <div class="flex items-center">
                                     <span class="text-white font-medium">{{ $member->username }}</span>
                                 </div>
                             </td>
                             <td class="px-6 py-4">
                                 <div class="text-left">
                                     <p class="text-white font-medium text-lg">Rp {{ number_format($member->balance, 0, ',', '.') }}</p>
                                 </div>
                             </td>
                         </tr>
                         @empty
                         <tr class="animate-on-scroll-zoom">
                             <td colspan="3" class="px-6 py-4 text-center text-gray-400">
                                 <div class="flex flex-col items-center gap-2">
                                     <svg class="w-12 h-12 text-gray-500 animate-on-scroll-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                     </svg>
                                     <span class="text-lg font-medium animate-on-scroll">Tidak ada data member</span>
                                     <span class="text-sm animate-on-scroll animate-on-scroll-delay-1">Belum ada member yang terdaftar</span>
                                 </div>
                             </td>
                         </tr>
                         @endforelse
                     </tbody>
                 </table>
             </div>
         </div>

                 <!-- Games Populer -->
         <div class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 border border-gray-700/30 rounded-lg p-6 animate-on-scroll-scale">
             <div class="flex items-center justify-between mb-6">
                 <h3 class="text-lg font-semibold text-white animate-on-scroll">Games Populer</h3>
             </div>
             <div class="overflow-x-auto">
                 <table class="w-full bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30">
                     <thead>
                         <tr class="border-b border-gray-700/30 animate-on-scroll-slide-up">
                             <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-1">Peringkat</th>
                             <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-2">Games</th>
                             <th class="px-6 py-4 text-left text-sm font-medium text-gray-300 uppercase tracking-wider animate-on-scroll animate-on-scroll-delay-3">Pembelian</th>
                         </tr>
                     </thead>
                     <tbody class="divide-y divide-gray-700/30">
                         @forelse($popularGames as $index => $game)
                         <tr class="hover:bg-gray-700/20 transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-{{ min($index + 1, 4) }}">
                             <td class="px-6 py-4">
                                 <div class="flex items-center">
                                     @if($index == 0)
                                         <div class="w-8 h-8 bg-gradient-to-r from-yellow-500 to-yellow-400 rounded-full flex items-center justify-center">
                                             <span class="text-white font-bold text-sm">{{ $index + 1 }}</span>
                                         </div>
                                     @elseif($index == 1)
                                         <div class="w-8 h-8 bg-gradient-to-r from-gray-400 to-gray-300 rounded-full flex items-center justify-center">
                                             <span class="text-white font-bold text-sm">{{ $index + 1 }}</span>
                                         </div>
                                     @else
                                         <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-orange-400 rounded-full flex items-center justify-center">
                                             <span class="text-white font-bold text-sm">{{ $index + 1 }}</span>
                                         </div>
                                     @endif
                                 </div>
                             </td>
                             <td class="px-6 py-4">
                                 <div class="flex items-center">
                                     <span class="text-white font-medium">{{ $game->name }}</span>
                                 </div>
                             </td>
                             <td class="px-6 py-4">
                                 <div class="text-left">
                                     <p class="text-white font-medium text-lg">{{ number_format($game->purchase_count) }}x</p>
                                 </div>
                             </td>
                         </tr>
                         @empty
                         <tr class="animate-on-scroll-zoom">
                             <td colspan="3" class="px-6 py-4 text-center text-gray-400">
                                 <div class="flex flex-col items-center gap-2">
                                     <svg class="w-12 h-12 text-gray-500 animate-on-scroll-rotate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                     </svg>
                                     <span class="text-lg font-medium animate-on-scroll">Tidak ada data games</span>
                                     <span class="text-sm animate-on-scroll animate-on-scroll-delay-1">Belum ada games yang ditambahkan</span>
                                 </div>
                             </td>
                         </tr>
                         @endforelse
                     </tbody>
                 </table>
             </div>
         </div>
    </div>

         <!-- Grafik Pembelian -->
     <div class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 border border-gray-700/30 rounded-lg p-6 animate-on-scroll-flip">
         <div class="flex items-center justify-between mb-6">
             <h3 class="text-lg font-semibold text-white animate-on-scroll">Grafik Pembelian</h3>
             <div class="flex items-center gap-2 animate-on-scroll animate-on-scroll-delay-1">
                 <span class="text-gray-400 text-sm">7 Hari Terakhir</span>
                 <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
             </div>
         </div>
         
                   <!-- Chart Container -->
          <div class="h-64 bg-gray-800/30 rounded-lg border border-gray-700/30 p-4 animate-on-scroll-slide-up">
              <canvas id="purchaseChart" width="400" height="200"></canvas>
          </div>
         
                             </div>
      </div>
 </div>

 <!-- Chart.js Script -->
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 <script>
     // Chart.js Configuration
     const ctx = document.getElementById('purchaseChart').getContext('2d');
     
     // Gradient for chart background
     const gradient = ctx.createLinearGradient(0, 0, 0, 200);
     gradient.addColorStop(0, 'rgba(147, 51, 234, 0.3)');
     gradient.addColorStop(1, 'rgba(147, 51, 234, 0.1)');
     
     const purchaseChart = new Chart(ctx, {
         type: 'bar',
         data: {
             labels: @json($chartData['labels']),
             datasets: [{
                 label: 'Pembelian',
                 data: @json($chartData['data']),
                 backgroundColor: [
                     'rgba(147, 51, 234, 0.8)',
                     'rgba(147, 51, 234, 0.8)',
                     'rgba(147, 51, 234, 0.8)',
                     'rgba(147, 51, 234, 0.8)',
                     'rgba(147, 51, 234, 0.8)',
                     'rgba(147, 51, 234, 0.8)',
                     'rgba(147, 51, 234, 0.8)'
                 ],
                 borderColor: [
                     'rgba(147, 51, 234, 1)',
                     'rgba(147, 51, 234, 1)',
                     'rgba(147, 51, 234, 1)',
                     'rgba(147, 51, 234, 1)',
                     'rgba(147, 51, 234, 1)',
                     'rgba(147, 51, 234, 1)',
                     'rgba(147, 51, 234, 1)'
                 ],
                 borderWidth: 2,
                 borderRadius: 8,
                 borderSkipped: false,
             }]
         },
         options: {
             responsive: true,
             maintainAspectRatio: false,
             plugins: {
                 legend: {
                     display: false
                 },
                 tooltip: {
                     backgroundColor: 'rgba(17, 24, 39, 0.9)',
                     titleColor: '#ffffff',
                     bodyColor: '#ffffff',
                     borderColor: 'rgba(147, 51, 234, 0.5)',
                     borderWidth: 1,
                     cornerRadius: 8,
                     displayColors: false,
                     callbacks: {
                         label: function(context) {
                             return 'Pembelian: ' + context.parsed.y + ' transaksi';
                         }
                     }
                 }
             },
             scales: {
                 y: {
                     beginAtZero: true,
                     grid: {
                         color: 'rgba(75, 85, 99, 0.2)',
                         drawBorder: false
                     },
                     ticks: {
                         color: '#9ca3af',
                         font: {
                             size: 12
                         },
                         callback: function(value) {
                             return value + 'x';
                         }
                     }
                 },
                 x: {
                     grid: {
                         display: false
                     },
                     ticks: {
                         color: '#9ca3af',
                         font: {
                             size: 12
                         }
                     }
                 }
             },
             interaction: {
                 intersect: false,
                 mode: 'index'
             },
             animation: {
                 duration: 1000,
                 easing: 'easeInOutQuart'
             }
         }
     });
 </script>
 @endsection 