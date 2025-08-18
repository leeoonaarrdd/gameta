@extends('admin.layouts.app')

@section('title', 'Edit Produk - Admin Panel')

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
            <h1 class="text-2xl sm:text-3xl font-bold text-white animate-on-scroll">Edit Produk</h1>
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
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-lg text-red-400 animate-on-scroll-zoom">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Form Fields -->
            <div class="space-y-4 sm:space-y-6">
                <!-- Nama Produk -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                    <label for="name" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Nama Produk</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $product->name) }}"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('name') border-red-500 @enderror"
                        placeholder="Masukkan nama produk"
                        required
                    >
                </div>
                @error('name')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Icon -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                    <label for="icon_id" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Icon</label>
                    <select 
                        id="icon_id" 
                        name="icon_id" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('icon_id') border-red-500 @enderror"
                    >
                        <option value="">Pilih Icon (Opsional)</option>
                        @foreach($icons as $icon)
                            <option value="{{ $icon->id }}" {{ old('icon_id', $product->icon_id) == $icon->id ? 'selected' : '' }}>
                                {{ $icon->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('icon_id')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Games -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                    <label for="game_id" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Games</label>
                    <select 
                        id="game_id" 
                        name="game_id" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('game_id') border-red-500 @enderror"
                        required
                    >
                        <option value="">Pilih Games</option>
                        @foreach($games as $game)
                            <option value="{{ $game->id }}" {{ old('game_id', $product->game_id) == $game->id ? 'selected' : '' }}>
                                {{ $game->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('game_id')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Provider -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                    <label for="provider" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Provider</label>
                    <select 
                        id="provider" 
                        name="provider" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('provider') border-red-500 @enderror"
                        required
                    >
                        <option value="">Pilih Provider</option>
                        <option value="Manual" {{ old('provider', $product->provider) == 'Manual' ? 'selected' : '' }}>Manual</option>
                        <option value="Digiflazz" {{ old('provider', $product->provider) == 'Digiflazz' ? 'selected' : '' }}>Digiflazz</option>
                    </select>
                </div>
                @error('provider')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- SKU -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                    <label for="sku" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">SKU</label>
                    <input 
                        type="text" 
                        id="sku" 
                        name="sku" 
                        value="{{ old('sku', $product->sku) }}"
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('sku') border-red-500 @enderror"
                        placeholder="Masukkan SKU"
                        required
                    >
                </div>
                @error('sku')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Harga Tamu -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                    <label for="price_tamu" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Harga Tamu</label>
                    <div class="w-full sm:w-110 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm font-medium">Rp</span>
                        </div>
                        <input 
                            type="number" 
                            id="price_tamu" 
                            name="price_tamu" 
                            value="{{ old('price_tamu', $product->price_tamu) }}"
                            class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg pl-10 pr-3 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('price_tamu') border-red-500 @enderror"
                            style="appearance: textfield; -webkit-appearance: textfield; -moz-appearance: textfield;"
                            min="0"
                            required
                        >
                    </div>
                </div>
                @error('price_tamu')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Harga Member -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                    <label for="price_member" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Harga Member</label>
                    <div class="w-full sm:w-110 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm font-medium">Rp</span>
                        </div>
                        <input 
                            type="number" 
                            id="price_member" 
                            name="price_member" 
                            value="{{ old('price_member', $product->price_member) }}"
                            class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg pl-10 pr-3 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('price_member') border-red-500 @enderror"
                            style="appearance: textfield; -webkit-appearance: textfield; -moz-appearance: textfield;"
                            placeholder="0"
                            min="0"
                            required
                        >
                    </div>
                </div>
                @error('price_member')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                                 <!-- Harga Asli (untuk Digiflazz) -->
                 <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                     <label for="original_price" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Harga Asli</label>
                     <div class="w-full sm:w-110 relative">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                             <span class="text-gray-400 text-sm font-medium">Rp</span>
                         </div>
                         <input 
                             type="number" 
                             id="original_price" 
                             name="original_price" 
                             value="{{ old('original_price', $product->original_price) }}"
                             class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg pl-10 pr-3 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('original_price') border-red-500 @enderror"
                             style="appearance: textfield; -webkit-appearance: textfield; -moz-appearance: textfield;"
                             min="0"
                         >
                     </div>
                 </div>
                 @error('original_price')
                     <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                         <div class="w-full sm:w-32"></div>
                         <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                     </div>
                 @enderror

                <!-- Margin Tamu -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                    <label for="margin_tamu" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Margin Tamu</label>
                    <div class="w-full sm:w-110 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm font-medium">+</span>
                        </div>
                        <input 
                            type="number" 
                            id="margin_tamu" 
                            name="margin_tamu" 
                            value="{{ old('margin_tamu', $product->margin_tamu) }}"
                            class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg pl-10 pr-3 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('margin_tamu') border-red-500 @enderror"
                            style="appearance: textfield; -webkit-appearance: textfield; -moz-appearance: textfield;"
                            min="0"
                        >
                    </div>
                </div>
                @error('margin_tamu')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Margin Member -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                    <label for="margin_member" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Margin Member</label>
                    <div class="w-full sm:w-110 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm font-medium">+</span>
                        </div>
                        <input 
                            type="number" 
                            id="margin_member" 
                            name="margin_member" 
                            value="{{ old('margin_member', $product->margin_member) }}"
                            class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg pl-10 pr-3 py-2 sm:py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('margin_member') border-red-500 @enderror"
                            style="appearance: textfield; -webkit-appearance: textfield; -moz-appearance: textfield;"
                            min="0"
                        >
                    </div>
                </div>
                @error('margin_member')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror

                                 <!-- Auto Update Price -->
                 <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                     <label for="auto_update_price" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Auto Update</label>
                     <div class="w-full sm:w-110">
                         <label class="flex items-center cursor-pointer">
                             <input 
                                 type="checkbox" 
                                 id="auto_update_price" 
                                 name="auto_update_price" 
                                 value="1"
                                 {{ old('auto_update_price', $product->auto_update_price) ? 'checked' : '' }}
                                 class="w-4 h-4 text-purple-600 bg-gray-800/50 border-gray-600/30 rounded focus:ring-purple-400/50 focus:ring-2 @error('auto_update_price') border-red-500 @enderror"
                             >
                             <span class="ml-2 text-sm text-gray-300">Aktifkan auto update harga dari Digiflazz</span>
                         </label>
                         @if($product->last_price_update)
                             <div class="text-xs text-gray-400 mt-1">
                                 Terakhir update: {{ $product->last_price_update->format('d/m/Y H:i') }}
                             </div>
                         @endif
                     </div>
                 </div>
                 @error('auto_update_price')
                     <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                         <div class="w-full sm:w-32"></div>
                         <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                     </div>
                 @enderror

                <!-- Status -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                    <label for="is_active" class="text-white font-medium w-full sm:w-32 flex-shrink-0 text-sm sm:text-base">Status</label>
                    <select 
                        id="is_active" 
                        name="is_active" 
                        class="w-full sm:w-110 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm sm:text-base @error('is_active') border-red-500 @enderror"
                        required
                    >
                        <option value="">Pilih status</option>
                        <option value="1" {{ old('is_active', $product->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $product->is_active) == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                @error('is_active')
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-4">
                        <div class="w-full sm:w-32"></div>
                        <p class="text-red-400 text-xs sm:text-sm w-full sm:w-110">{{ $message }}</p>
                    </div>
                @enderror
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

<script src="/js/products.js"></script>
@endsection
