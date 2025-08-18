@forelse($socialMedia as $index => $social)
    <tr class="hover:bg-gray-700/20 transition-colors duration-200">
        <td class="px-6 py-4 text-sm text-white">{{ $socialMedia->firstItem() + $index }}</td>
        <td class="px-6 py-4 text-sm text-white">
            <div class="flex items-center">
                <i class="{{ $social->icon }} text-2xl text-purple-400"></i>
            </div>
        </td>
        <td class="px-6 py-4 text-sm text-white">
            <div class="max-w-xs truncate">
                <a href="{{ $social->link }}" target="_blank" class="text-gray-300 hover:text-gray-300 transition-colors duration-200 cursor-pointer">
                    {{ $social->link }}
                </a>
            </div>
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center space-x-3">
                <button 
                    onclick="SocialMediaPage.editSocialMedia({{ $social->id }}, {{ json_encode($social->icon) }}, {{ json_encode($social->link) }})"
                    class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1 cursor-pointer" 
                    title="Edit"
                >
                    <i class="fas fa-pencil-alt text-xs"></i>
                    Edit
                </button>
                <button 
                    class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1 cursor-pointer" 
                    title="Delete"
                    data-url="{{ route('admin.social-media.destroy', $social) }}"
                    data-item-name="sosial media '{{ $social->icon }}'"
                    data-message="Apakah Anda yakin ingin menghapus sosial media '{{ $social->icon }}'? Tindakan ini tidak dapat dibatalkan dan link sosial media akan terhapus permanen."
                >
                    <i class="fas fa-trash text-xs"></i>
                    Hapus
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="px-6 py-4 text-sm text-gray-400 text-center">
            Tidak ada sosial media ditemukan
        </td>
    </tr>
@endforelse
