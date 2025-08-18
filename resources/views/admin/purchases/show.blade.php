@extends('admin.layouts.app')

@section('title', 'Detail Pembelian - Admin Panel')

@section('content')
<div class="w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-white">Detail Pembelian</h1>
        </div>
    </div>

    <!-- Purchase Details -->
    <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-6 mb-8">
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Username</span>
                <span class="text-white font-medium">{{ $purchase->display_username }}</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">No. Whatsapp</span>
                @php
                    $notes = json_decode($purchase->notes, true) ?? [];
                    $whatsapp = $notes['whatsapp'] ?? '-';
                @endphp
                <span class="text-white font-medium">{{ $whatsapp }}</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Order ID</span>
                <span class="text-white font-mono">{{ $purchase->order_id }}</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Produk</span>
                <span class="text-white font-medium">{{ $purchase->product->name ?? '-' }}</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Metode</span>
                <span class="text-white font-medium">{{ $purchase->payment_method ?? '-' }}</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Games</span>
                <span class="text-white font-medium">{{ $purchase->product->game->name ?? '-' }}</span>
            </div>
            
            @php
                $notes = json_decode($purchase->notes, true) ?? [];
                $playerFields = $notes['player_fields'] ?? [];
                $playerId = $notes['player_id'] ?? '-';
                $playerNickname = $notes['player_nickname'] ?? '-';
                $adminFee = $notes['admin_fee'] ?? 0;
                $uniqueCode = $notes['unique_code'] ?? 0;
            @endphp
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Target</span>
                <span class="text-white font-medium">
                    @if(!empty($playerFields) && is_array($playerFields))
                        @if(count($playerFields) === 1)
                            {{ $playerFields[0] }}
                        @else
                            {{ $playerFields[0] }} ({{ implode(' - ', array_slice($playerFields, 1)) }})
                        @endif
                    @elseif(!empty($playerId))
                        {{ $playerId }}
                    @else
                        -
                    @endif
                </span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Nickname</span>
                <span class="text-white font-medium">{{ $playerNickname }}</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Biaya Admin</span>
                <span class="text-white font-medium">Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Kode Unik</span>
                <span class="text-white font-medium">Rp {{ number_format($uniqueCode, 0, ',', '.') }}</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Total</span>
                <span class="text-white font-medium">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Status</span>
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full border {{ $purchase->status_badge }}">
                    {{ $purchase->status_text }}
                </span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Keterangan</span>
                <span class="text-white font-medium">-</span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Tanggal</span>
                <span class="text-white font-medium">{{ $purchase->created_at->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-end items-center pt-6 gap-3">
            <a href="{{ route('admin.purchases.index') }}" class="text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
            
            <button onclick="window.print()" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base flex items-center gap-2" style="cursor: pointer;">
                <i class="fas fa-print"></i>
                Cetak
            </button>
        </div>
        </div>
        </div>
    </div>
</div>
@endsection
