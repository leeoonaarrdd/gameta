@forelse($faqs as $index => $faq)
<tr class="hover:bg-gray-700/20 transition-colors duration-200">
    <td class="px-6 py-4 text-sm text-gray-300">
        {{ $faqs->firstItem() + $index }}
    </td>
    <td class="px-6 py-4">
        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-purple-500/20 text-purple-400 border border-purple-500/30">
            {{ $faq->kategori }}
        </span>
    </td>
    <td class="px-6 py-4">
        <div class="text-sm">
            <div class="text-white font-medium">{{ Str::limit($faq->pertanyaan, 50) }}</div>
        </div>
    </td>
    <td class="px-6 py-4 text-sm text-gray-300">
        {{ Str::limit($faq->konten, 100) }}
    </td>
    <td class="px-6 py-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" title="Edit">
                <i class="fas fa-pencil-alt text-xs"></i>
                Edit
            </a>
            <button 
                class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 cursor-pointer rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1" 
                title="Delete"
                data-url="{{ route('admin.faqs.destroy', $faq) }}"
                data-item-name="pertanyaan umum '{{ Str::limit($faq->pertanyaan, 30) }}'"
                data-message="Apakah Anda yakin ingin menghapus pertanyaan umum '{{ Str::limit($faq->pertanyaan, 30) }}'? Tindakan ini tidak dapat dibatalkan."
            >
                <i class="fas fa-trash text-xs"></i>
                Hapus
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="px-6 py-4 text-center text-gray-400">Tidak ada data pertanyaan umum</td>
</tr>
@endforelse
