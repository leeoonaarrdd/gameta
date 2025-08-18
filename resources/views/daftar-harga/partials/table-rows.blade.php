@forelse($products as $product)
    <tr class="hover:bg-gray-700/20 transition-colors duration-200"></tr>
        <td class="px-6 py-4 text-sm text-gray-300">{{ $product->game->name ?? 'N/A' }}</td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                @if($product->icon && $product->icon->file_path)
                    <div class="flex-shrink-0">
                        <img 
                            src="{{ asset('storage/' . $product->icon->file_path) }}" 
                            alt="{{ $product->icon->name }}"
                            class="w-10 h-10 rounded-lg object-cover"
                            onerror="this.style.display='none'"
                        >
                    </div>
                @else
                    <div class="flex-shrink-0 w-8 h-8 bg-gray-700/50 rounded-lg border border-gray-600/30 flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-xs"></i>
                    </div>
                @endif
                <div class="text-sm">
                    <div class="text-white font-medium">{{ $product->name }}</div>
                </div>
            </div>
        </td>
        <td class="px-6 py-4 text-sm text-white">Rp {{ number_format($product->price_tamu, 0, ',', '.') }}</td>
        <td class="px-6 py-4 text-sm text-white">Rp {{ number_format($product->price_member, 0, ',', '.') }}</td>
        <td class="px-6 py-4">
            @if($product->is_active)
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-purple-500/20 text-purple-400 border border-purple-500/30">Aktif</span>
            @else
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-500/20 text-gray-400 border border-gray-500/30">Tidak Aktif</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
            <div class="flex flex-col items-center gap-2">
                <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.881-6.08-2.33"/>
                </svg>
                <span class="text-lg font-medium">Tidak ada produk tersedia</span>
                <span class="text-sm">Belum ada produk yang ditambahkan ke dalam sistem</span>
            </div>
        </td>
    </tr>
@endforelse
