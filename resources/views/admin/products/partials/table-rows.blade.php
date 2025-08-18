@forelse($products as $index => $product)
<tr class="hover:bg-gray-700/20 transition-colors duration-200">
    <td class="px-6 py-4 text-sm text-white">
        {{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }}
    </td>
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
         <td class="px-6 py-4 text-sm text-white">{{ $product->game->name ?? '-' }}</td>
     <td class="px-6 py-4 text-sm text-white">
         Rp {{ number_format($product->price_tamu, 0, ',', '.') }}
     </td>
     <td class="px-6 py-4 text-sm text-white">
         Rp {{ number_format($product->price_member, 0, ',', '.') }}
     </td>
    <td class="px-6 py-4">
        @if($product->is_active)
            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-purple-500/20 text-purple-400 border border-purple-500/30">Aktif</span>
        @else
            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Nonaktif</span>
        @endif
    </td>
    <td class="px-6 py-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.products.edit', $product) }}" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" title="Edit">
                <i class="fas fa-pencil-alt text-xs"></i>
                Edit
            </a>
            <button class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" title="Delete"
                data-url="{{ route('admin.products.destroy', $product) }}"
                data-item-name="produk '{{ $product->name }}'"
                data-message="Apakah Anda yakin ingin menghapus produk '{{ $product->name }}'? Tindakan ini tidak dapat dibatalkan."
            >
                <i class="fas fa-trash text-xs"></i>
                Hapus
            </button>
        </div>
    </td>
</tr>
 @empty
 <tr>
     <td colspan="7" class="px-6 py-4 text-center text-gray-400">Tidak ada data produk</td>
 </tr>
@endforelse
