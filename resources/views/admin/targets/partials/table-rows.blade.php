@forelse($targets as $index => $target)
<tr class="hover:bg-gray-700/20 transition-colors duration-200">
    <td class="px-6 py-4 text-sm text-white">{{ $targets->firstItem() + $index }}</td>
    <td class="px-6 py-4 text-sm text-white">{{ $target->judul_target }}</td>
    <td class="px-6 py-4 text-sm text-white">{{ $target->teks_header }}</td>
    <td class="px-6 py-4 text-sm text-white">
        <span class="text-white">{{ $target->total_fields_count }} kolom</span>
    </td>
    <td class="px-6 py-4 text-sm text-white">{{ $target->sparator ?: '-' }}</td>
    <td class="px-6 py-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.targets.edit', $target) }}" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" title="Edit">
                <i class="fas fa-pencil-alt text-xs"></i>
                Edit
            </a>
            <button 
                class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" 
                title="Delete"
                data-url="{{ route('admin.targets.destroy', $target) }}"
                data-item-name="target '{{ $target->judul_target }}'"
                data-message="Apakah Anda yakin ingin menghapus target '{{ $target->judul_target }}'? Semua data target yang terkait akan terhapus permanen."
            >
                <i class="fas fa-trash text-xs"></i>
                Hapus
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-400">
        Tidak ada data target yang ditemukan.
    </td>
</tr>
@endforelse
