@forelse($topups as $index => $topup)
    <tr class="hover:bg-gray-700/20 transition-colors duration-200">
        <td class="px-6 py-4 text-sm text-white">{{ $topups->firstItem() + $index }}</td>
        <td class="px-6 py-4 text-sm text-white font-mono">{{ $topup->username }}</td>
        <td class="px-6 py-4 text-sm text-white font-mono">
            <button 
                onclick="TopupPage.viewTopup({{ $topup->id }})"
                class="text-purple-400 hover:text-purple-300 transition-colors duration-200 cursor-pointer"
            >
                {{ $topup->topup_id }}
            </button>
        </td>
        <td class="px-6 py-4 text-sm text-white">
            Rp {{ number_format($topup->jumlah, 0, ',', '.') }}
        </td>
        <td class="px-6 py-4">
            @if($topup->status === 'success')
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Success</span>
            @elseif($topup->status === 'pending')
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending</span>
            @elseif($topup->status === 'failed')
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Failed</span>
            @else
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-gray-500/20 text-gray-400 border border-gray-500/30">Cancelled</span>
            @endif
        </td>
        <td class="px-6 py-4 text-sm text-white">
            {{ $topup->tanggal ? $topup->tanggal->format('d/m/Y H:i') : '-' }}
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center space-x-3">
                <button 
                    onclick="TopupPage.editTopup({{ $topup->id }})"
                    class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1 cursor-pointer" 
                    title="Edit"
                >
                    <i class="fas fa-pencil-alt text-xs"></i>
                    Edit
                </button>
                <button 
                    class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1 cursor-pointer" 
                    title="Hapus Topup"
                    data-url="{{ route('admin.topups.destroy', $topup) }}"
                    data-item-name="topup '{{ $topup->topup_id }}'"
                    data-message="Apakah Anda yakin ingin menghapus topup '{{ $topup->topup_id }}'? Tindakan ini tidak dapat dibatalkan dan semua data topup akan terhapus permanen."
                >
                    <i class="fas fa-trash text-xs"></i>
                    Hapus
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-4 text-sm text-gray-400 text-center">
            Tidak ada data topup ditemukan
        </td>
    </tr>
@endforelse
