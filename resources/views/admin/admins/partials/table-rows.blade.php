@forelse($admins as $index => $admin)
    <tr class="hover:bg-gray-700/20 transition-colors duration-200">
        <td class="px-6 py-4 text-sm text-white">{{ $admins->firstItem() + $index }}</td>
        <td class="px-6 py-4 text-sm text-white">{{ $admin->name }}</td>
        <td class="px-6 py-4 text-sm text-white">{{ $admin->username }}</td>
        <td class="px-6 py-4">
            @if($admin->status === 'active')
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-purple-500/20 text-purple-400 border border-purple-500/30">Aktif</span>
            @else
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Tidak Aktif</span>
            @endif
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center space-x-3">
                <button 
                    onclick="AdminPage.editAdmin({{ $admin->id }}, '{{ $admin->name }}', '{{ $admin->username }}', '{{ $admin->status }}')"
                    class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1 cursor-pointer" 
                    title="Edit"
                >
                    <i class="fas fa-pencil-alt text-xs"></i>
                    Edit
                </button>
                <button 
                    class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1 cursor-pointer" 
                    title="Delete"
                    data-url="{{ route('admin.admins.destroy', $admin) }}"
                    data-item-name="admin '{{ $admin->name }}'"
                    data-message="Apakah Anda yakin ingin menghapus admin '{{ $admin->name }}'? Tindakan ini tidak dapat dibatalkan dan admin tidak akan dapat mengakses sistem lagi."
                >
                    <i class="fas fa-trash text-xs"></i>
                    Hapus
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-6 py-4 text-sm text-gray-400 text-center">
            Tidak ada admin ditemukan
        </td>
    </tr>
@endforelse
