@forelse($purchases as $index => $purchase)
<tr class="hover:bg-gray-700/20 transition-colors duration-200">
    <td class="px-6 py-4 text-sm text-white">
        {{ ($purchases->currentPage() - 1) * $purchases->perPage() + $index + 1 }}
    </td>
    <td class="px-6 py-4">
        <div class="text-sm">
            <div class="text-white font-medium">{{ $purchase->display_username }}</div>
        </div>
    </td>
    <td class="px-6 py-4 text-sm text-white font-mono">
        <a href="{{ route('admin.purchases.show', $purchase) }}" class="text-purple-400 hover:text-purple-300 transition-colors duration-200 cursor-pointer">
            {{ $purchase->order_id }}
        </a>
    </td>
    <td class="px-6 py-4">
        <div class="text-sm">
            <div class="text-white font-medium">{{ $purchase->product->name ?? '-' }}</div>
            <div class="text-gray-400 text-xs">{{ $purchase->product->game->name ?? '-' }}</div>
        </div>
    </td>
    <td class="px-6 py-4 text-sm text-white">
        Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
    </td>
    <td class="px-6 py-4">
        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full border {{ $purchase->status_badge }}">
            {{ $purchase->status_text }}
        </span>
    </td>
    <td class="px-6 py-4 text-sm text-white">
        <div>
            <div>{{ $purchase->created_at->format('d/m/Y') }}</div>
            <div class="text-gray-400 text-xs">{{ $purchase->created_at->format('H:i') }}</div>
        </div>
    </td>
    <td class="px-6 py-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.purchases.edit', $purchase) }}" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" title="Edit">
                <i class="fas fa-pencil-alt text-xs"></i>
                Edit
            </a>
            <button 
                class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" 
                title="Delete"
                data-url="{{ route('admin.purchases.destroy', $purchase) }}"
                data-item-name="pembelian '{{ $purchase->order_id }}'"
                data-message="Apakah Anda yakin ingin menghapus pembelian '{{ $purchase->order_id }}'? Tindakan ini tidak dapat dibatalkan dan semua data pembelian akan terhapus permanen."
            >
                <i class="fas fa-trash text-xs"></i>
                Hapus
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="px-6 py-4 text-center text-gray-400">Tidak ada data pembelian</td>
</tr>
@endforelse
