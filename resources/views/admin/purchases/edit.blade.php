@extends('admin.layouts.app')

@section('title', 'Edit Pembelian - Admin Panel')

@section('content')
<style>
/* Hide number input arrows */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}
</style>

<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-6 sm:mb-8 animate-on-scroll-bounce">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-white animate-on-scroll">Edit Pembelian</h1>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/30 p-4 sm:p-6 lg:p-8 shadow-xl animate-on-scroll-scale">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-lg text-green-400 animate-on-scroll-zoom">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 animate-on-scroll-zoom">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 animate-on-scroll-zoom      ">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.purchases.update', $purchase) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Form Fields -->
            <div class="space-y-4 sm:space-y-6">
                <!-- Username -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Username</label>
                    <div class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white text-sm sm:text-base">
                        {{ $purchase->display_username }}
                    </div>
                </div>

                <!-- No. Whatsapp -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">No. Whatsapp</label>
                    <div class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white text-sm sm:text-base">
                        @php
                            $notes = json_decode($purchase->notes, true) ?? [];
                            $whatsapp = $notes['whatsapp'] ?? '-';
                        @endphp
                        {{ $whatsapp }}
                    </div>
                </div>

                <!-- Order ID -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Order ID</label>
                    <div class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white text-sm sm:text-base">
                        {{ $purchase->order_id }}
                    </div>
                </div>

                <!-- Produk -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="product_id" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Produk</label>
                    <select 
                        id="product_id" 
                        name="product_id" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('product_id') border-red-500 @enderror"
                        required
                    >
                        @foreach(\App\Models\Product::with('game')->get() as $product)
                            <option value="{{ $product->id }}" {{ $purchase->product_id == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} - {{ $product->game->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('product_id')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Metode -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up      ">
                    <label for="payment_method" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Metode</label>
                    <select 
                        id="payment_method" 
                        name="payment_method" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('payment_method') border-red-500 @enderror"
                        required
                    >
                        @foreach(\App\Models\PaymentMethod::all() as $method)
                            <option value="{{ $method->name }}" {{ $purchase->payment_method == $method->name ? 'selected' : '' }}>
                                {{ $method->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('payment_method')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Games -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Games</label>
                    <div class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white text-sm sm:text-base">
                        {{ $purchase->product->game->name ?? '-' }}
                    </div>
                </div>

                <!-- Target -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="player_id" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Target</label>
                    <input 
                        type="text" 
                        id="player_id" 
                        name="player_id" 
                        value="{{ json_decode($purchase->notes, true)['player_id'] ?? '-' }}"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('player_id') border-red-500 @enderror"
                        placeholder="Masukkan target/player ID"
                    >
                </div>
                @error('player_id')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Nickname -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="player_nickname" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Nickname</label>
                    <input 
                        type="text" 
                        id="player_nickname" 
                        name="player_nickname" 
                        value="{{ json_decode($purchase->notes, true)['player_nickname'] ?? '-' }}"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('player_nickname') border-red-500 @enderror"
                        placeholder="Masukkan nickname"
                    >
                </div>
                @error('player_nickname')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Biaya Admin -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="admin_fee" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Biaya Admin</label>
                    <div class="w-full sm:w-110 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm font-medium">Rp</span>
                        </div>
                        <input 
                            type="number" 
                            id="admin_fee" 
                            name="admin_fee" 
                            value="{{ json_decode($purchase->notes, true)['admin_fee'] ?? 0 }}"
                            class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg pl-10 pr-3 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('admin_fee') border-red-500 @enderror"
                            style="appearance: textfield; -webkit-appearance: textfield; -moz-appearance: textfield;"
                            min="0"
                        >
                    </div>
                </div>
                @error('admin_fee')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Kode Unik -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="unique_code" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Kode Unik</label>
                    <input 
                        type="number" 
                        id="unique_code" 
                        name="unique_code" 
                        value="{{ json_decode($purchase->notes, true)['unique_code'] ?? 0 }}"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('unique_code') border-red-500 @enderror"
                        style="appearance: textfield; -webkit-appearance: textfield; -moz-appearance: textfield;"
                        min="0"
                    >
                </div>
                @error('unique_code')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Total -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="total_amount" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Total</label>
                    <div class="w-full sm:w-110 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm font-medium">Rp</span>
                        </div>
                        <input 
                            type="number" 
                            id="total_amount" 
                            name="total_amount" 
                            value="{{ $purchase->total_amount }}"
                            class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg pl-10 pr-3 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('total_amount') border-red-500 @enderror"
                            style="appearance: textfield; -webkit-appearance: textfield; -moz-appearance: textfield;"
                            min="0"
                            required
                        >
                    </div>
                </div>
                @error('total_amount')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Status -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="status" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Status</label>
                    <select 
                        id="status" 
                        name="status" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('status') border-red-500 @enderror"
                        required
                    >
                        <option value="pending" {{ $purchase->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $purchase->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $purchase->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $purchase->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="failed" {{ $purchase->status == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                @error('status')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Keterangan -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label for="notes" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Keterangan</label>
                    <input 
                        type="text" 
                        id="notes" 
                        name="notes" 
                        value="-"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('notes') border-red-500 @enderror"
                        placeholder="Masukkan keterangan"
                    >
                </div>
                @error('notes')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Tanggal -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4 animate-on-scroll-slide-up">
                    <label class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Tanggal</label>
                    <div class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white text-sm sm:text-base">
                        {{ $purchase->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-6 sm:mt-8 animate-on-scroll-fade">
                <!-- Right Side Buttons -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto sm:ml-auto animate-on-scroll-fade">
                    <button type="button" onclick="window.history.back()" class="text-white hover:text-gray-300 transition-colors duration-200 text-sm sm:text-base w-full sm:w-auto text-center cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-200 text-sm sm:text-base w-full sm:w-auto cursor-pointer">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
