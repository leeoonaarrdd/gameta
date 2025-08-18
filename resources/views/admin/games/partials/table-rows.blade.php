@forelse($games as $game)
<tr class="hover:bg-gray-700/20 transition-colors duration-200 game-row" data-game-id="{{ $game->id }}" data-order="{{ $game->order }}" title="Drag untuk mengubah urutan">
    <td class="px-6 py-4">
        <div class="w-16">
            @if($game->gambar)
                <img src="{{ Storage::url($game->gambar) }}" alt="{{ $game->name }}" class="w-full h-24 object-cover rounded-lg">
            @else
                <div class="w-full h-24 bg-gradient-to-b from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-gamepad text-white text-2xl"></i>
                </div>
            @endif
        </div>
    </td>
    <td class="px-6 py-4">
        <div class="text-sm">
            <div class="text-white font-medium">{{ $game->name }}</div>
            <div class="text-purple-400 text-xs mt-1">{{ $game->slug }}</div>
        </div>
    </td>
    <td class="px-6 py-4 text-sm text-white">{{ $game->sub_judul ?? '-' }}</td>
    <td class="px-6 py-4 text-sm text-white">{{ $game->category->name ?? '-' }}</td>
    <td class="px-6 py-4">
        @if($game->is_active)
            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-purple-500/20 text-purple-400 border border-purple-500/30">Aktif</span>
        @else
            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Nonaktif</span>
        @endif
    </td>
    <td class="px-6 py-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.games.edit', $game) }}" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" title="Edit">
                <i class="fas fa-pencil-alt text-xs"></i>
                Edit
            </a>
            <button class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" title="Delete"
                data-url="{{ route('admin.games.destroy', $game) }}"
                data-item-name="game '{{ $game->name }}'"
                data-message="Apakah Anda yakin ingin menghapus game '{{ $game->name }}'? Semua produk yang terkait dengan game ini akan terpengaruh."
            >
                <i class="fas fa-trash text-xs"></i>
                Hapus
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="px-6 py-4 text-center text-gray-400">Tidak ada data games</td>
</tr>
@endforelse
