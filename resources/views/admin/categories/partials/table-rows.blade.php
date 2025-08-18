@forelse($categories as $index => $category)
    <tr class="hover:bg-gray-700/20 transition-colors duration-200 category-row" data-category-id="{{ $category->id }}" data-order="{{ $category->order }}" title="Drag untuk mengubah urutan">
        <td class="px-6 py-4 text-sm text-white">{{ $category->order }}</td>
        <td class="px-6 py-4 text-sm text-white">{{ $category->name }}</td>
        <td class="px-6 py-4 text-sm text-white">{{ $category->games_count }} Games</td>
        <td class="px-6 py-4">
            <div class="flex items-center space-x-3">
                <button 
                    onclick="CategoryPage.editCategory({{ $category->id }}, '{{ $category->name }}')"
                    class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1 cursor-pointer" 
                    title="Edit"
                >
                    <i class="fas fa-pencil-alt text-xs"></i>
                    Edit
                </button>
                <button 
                    class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1 cursor-pointer" 
                    title="Delete"
                    data-url="{{ route('admin.categories.destroy', $category->id) }}"
                    data-item-name="kategori '{{ $category->name }}'"
                    data-message="Apakah Anda yakin ingin menghapus kategori '{{ $category->name }}'? Semua game yang terkait dengan kategori ini akan terpengaruh."
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
            Tidak ada kategori ditemukan
        </td>
    </tr>
@endforelse
