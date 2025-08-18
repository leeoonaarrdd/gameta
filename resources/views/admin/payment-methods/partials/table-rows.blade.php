@forelse($paymentMethods as $index => $paymentMethod)
<tr class="hover:bg-gray-700/20 transition-colors duration-200">
    <td class="px-6 py-4 text-sm text-white">
        {{ $paymentMethods->firstItem() + $index }}
    </td>
    <td class="px-6 py-4">
        <div class="w-24 h-8">
            @if($paymentMethod->image)
                <img src="{{ Storage::url($paymentMethod->image) }}" alt="{{ $paymentMethod->name }}" class="w-full h-full object-contain rounded-lg">
            @else
                <div class="w-full h-full bg-gradient-to-b from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-credit-card text-white text-sm"></i>
                </div>
            @endif
        </div>
    </td>
    <td class="px-6 py-4">
        <div class="text-sm">
            <div class="text-white font-medium">{{ $paymentMethod->name }}</div>
        </div>
    </td>
    <td class="px-6 py-4 text-sm text-white">{{ $paymentMethod->kategori }}</td>
    <td class="px-6 py-4 text-sm text-white">{{ $paymentMethod->provider }}</td>
    <td class="px-6 py-4">
        @if($paymentMethod->is_active)
            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-purple-500/20 text-purple-400 border border-purple-500/30">Aktif</span>
        @else
            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Nonaktif</span>
        @endif
    </td>
    <td class="px-6 py-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.payment-methods.edit', $paymentMethod) }}" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" title="Edit">
                <i class="fas fa-pencil-alt text-xs"></i>
                Edit
            </a>
            <button 
                class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" 
                title="Delete"
                data-url="{{ route('admin.payment-methods.destroy', $paymentMethod) }}"
                data-item-name="metode pembayaran '{{ $paymentMethod->name }}'"
                data-message="Apakah Anda yakin ingin menghapus metode pembayaran '{{ $paymentMethod->name }}'? Tindakan ini tidak dapat dibatalkan dan metode pembayaran tidak akan tersedia lagi."
            >
                <i class="fas fa-trash text-xs"></i>
                Hapus
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="px-6 py-4 text-center text-gray-400">Tidak ada data metode pembayaran</td>
</tr>
@endforelse
