@forelse($members as $index => $member)
    <tr class="hover:bg-gray-700/20 transition-colors duration-200">
        <td class="px-6 py-4 text-sm text-white">{{ $members->firstItem() + $index }}</td>
        <td class="px-6 py-4 text-sm text-white font-mono">
            <button 
                onclick="MemberPage.viewMember({{ $member->id }})"
                class="text-purple-400 hover:text-purple-300 transition-colors duration-200 cursor-pointer"
            >
                {{ $member->username }}
            </button>
        </td>
        <td class="px-6 py-4 text-sm text-white">{{ $member->phone ?? '-' }}</td>
        <td class="px-6 py-4 text-sm text-white">
            @if(isset($member->balance))
                Rp {{ number_format($member->balance, 0, ',', '.') }}
            @else
                Rp 0
            @endif
        </td>
        <td class="px-6 py-4">
            @if($member->phone_verified_at)
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-500/20 text-green-400 border border-green-500/30">Terverifikasi</span>
            @else
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Belum Verifikasi</span>
            @endif
        </td>
        <td class="px-6 py-4">
            @if($member->status === 'active')
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-purple-500/20 text-purple-400 border border-purple-500/30">Aktif</span>
            @else
                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-red-500/20 text-red-400 border border-red-500/30">Tidak Aktif</span>
            @endif
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center space-x-3">
                <button 
                    onclick="MemberPage.editMember({{ $member->id }})"
                    class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1 cursor-pointer" 
                    title="Edit"
                >
                    <i class="fas fa-pencil-alt text-xs"></i>
                    Edit
                </button>

                <button 
                    class="btn-delete bg-gradient-to-r from-red-500 to-red-400 hover:from-red-600 hover:to-red-500 text-white px-4 py-1.5 rounded-full text-xs font-medium transition-all duration-200 flex items-center gap-1 cursor-pointer" 
                    title="Hapus Member"
                    data-url="{{ route('admin.members.destroy', $member) }}"
                    data-item-name="member '{{ $member->username }}'"
                    data-message="Apakah Anda yakin ingin menghapus member '{{ $member->username }}'? Semua data terkait member ini akan terhapus permanen."
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
            Tidak ada member ditemukan
        </td>
    </tr>
@endforelse
