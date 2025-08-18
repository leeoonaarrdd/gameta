@extends('admin.layouts.app')

@section('title', 'Konten Halaman - Admin Panel')

@push('scripts')
<style>
/* Memastikan teks di CKEditor terlihat */
.ck-editor__editable {
    color: #000 !important;
    background-color: #fff !important;
}
.ck-editor__editable p {
    color: #000 !important;
}
.ck-editor__editable h1,
.ck-editor__editable h2,
.ck-editor__editable h3,
.ck-editor__editable h4,
.ck-editor__editable h5,
.ck-editor__editable h6 {
    color: #000 !important;
}


</style>
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabSyarat = document.getElementById('tab-syarat');
    const tabPrivasi = document.getElementById('tab-privasi');
    const contentSyarat = document.getElementById('content-syarat');
    const contentPrivasi = document.getElementById('content-privasi');
    
    tabSyarat.addEventListener('click', function() {
        // Update tab styles
        tabSyarat.classList.add('bg-purple-500', 'text-white');
        tabSyarat.classList.remove('text-gray-300');
        tabPrivasi.classList.remove('bg-purple-500', 'text-white');
        tabPrivasi.classList.add('text-gray-300');
        
        // Show/hide content
        contentSyarat.classList.remove('hidden');
        contentPrivasi.classList.add('hidden');
        
        // Update form action to indicate which tab is active
        document.querySelector('form').setAttribute('data-active-tab', 'syarat');
    });
    
    tabPrivasi.addEventListener('click', function() {
        // Update tab styles
        tabPrivasi.classList.add('bg-purple-500', 'text-white');
        tabPrivasi.classList.remove('text-gray-300');
        tabSyarat.classList.remove('bg-purple-500', 'text-white');
        tabSyarat.classList.add('text-gray-300');
        
        // Show/hide content
        contentPrivasi.classList.remove('hidden');
        contentSyarat.classList.add('hidden');
        
        // Update form action to indicate which tab is active
        document.querySelector('form').setAttribute('data-active-tab', 'privasi');
    });
    
    // Set initial active tab
    document.querySelector('form').setAttribute('data-active-tab', 'syarat');
    
    // Initialize CKEditor for Syarat Ketentuan
    ClassicEditor
        .create(document.querySelector('#syarat_ketentuan'), {
            toolbar: [
                'heading',
                '|',
                'bold',
                'italic',
                'link',
                '|',
                'outdent',
                'indent',
                '|',
                'blockQuote',
                'insertTable',
                'undo',
                'redo'
            ],
            language: 'id',
            placeholder: 'Masukkan konten syarat ketentuan di sini...'
        })
        .then(editor => {
            // Set konten awal jika ada
            @if(!empty($syaratKetentuan))
                editor.setData(`{!! addslashes($syaratKetentuan) !!}`);
            @endif
            
            // Update textarea saat konten berubah
            editor.model.document.on('change:data', () => {
                document.querySelector('#syarat_ketentuan').value = editor.getData();
            });
        })
        .catch(error => {
            console.error(error);
        });

    // Initialize CKEditor for Kebijakan Privasi
    ClassicEditor
        .create(document.querySelector('#kebijakan_privasi'), {
            toolbar: [
                'heading',
                '|',
                'bold',
                'italic',
                'link',
                '|',
                'outdent',
                'indent',
                '|',
                'blockQuote',
                'insertTable',
                'undo',
                'redo'
            ],
            language: 'id',
            placeholder: 'Masukkan konten kebijakan privasi di sini...'
        })
        .then(editor => {
            // Set konten awal jika ada
            @if(!empty($kebijakanPrivasi))
                editor.setData(`{!! addslashes($kebijakanPrivasi) !!}`);
            @endif
            
            // Update textarea saat konten berubah
            editor.model.document.on('change:data', () => {
                document.querySelector('#kebijakan_privasi').value = editor.getData();
            });
        })
        .catch(error => {
            console.error(error);
                });
    });
    
    // Form submission handler
    document.querySelector('form').addEventListener('submit', function(e) {
        const activeTab = this.getAttribute('data-active-tab');
        
        if (activeTab === 'syarat') {
            // Only submit syarat_ketentuan, clear kebijakan_privasi
            document.querySelector('#kebijakan_privasi').value = '';
        } else if (activeTab === 'privasi') {
            // Only submit kebijakan_privasi, clear syarat_ketentuan
            document.querySelector('#syarat_ketentuan').value = '';
        }
    });
</script>
@endpush

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8"> 

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-400 mr-3"></i>
                <span class="text-green-400">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                <span class="text-red-400">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="flex justify-center mb-6 animate-on-scroll-bounce">
        <div class="flex bg-gray-800/50 rounded-lg p-1 animate-on-scroll animate-on-scroll-delay-1">
            <button id="tab-syarat" class="px-6 py-3 text-sm font-medium text-white bg-purple-500 rounded-lg transition-colors duration-200 animate-on-scroll-zoom">
                Syarat Ketentuan
            </button>
            <button id="tab-privasi" class="px-6 py-3 text-sm font-medium text-gray-300 rounded-lg hover:text-white transition-colors duration-200 animate-on-scroll animate-on-scroll-delay-2">
                Kebijakan Privasi
            </button>
        </div>
    </div>

    <!-- Content Form -->
    <div class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 border border-gray-700/30 rounded-xl p-6 animate-on-scroll-zoom">
        <form action="{{ route('admin.content.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Syarat Ketentuan Tab Content -->
            <div id="content-syarat">
                <div>
                    <div class="flex items-center justify-between mb-2 animate-on-scroll-zoom animate-on-scroll-delay-1">
                        <label for="syarat_ketentuan" class="block text-sm font-medium text-gray-300">
                            Syarat Ketentuan
                        </label>
                        <div class="text-xs text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Editor dengan tools lengkap tersedia
                        </div>
                    </div>
                    <textarea id="syarat_ketentuan" name="syarat_ketentuan" class="w-full bg-white border border-gray-300 rounded-lg text-gray-900 p-3 animate-on-scroll-zoom animate-on-scroll-delay-2" style="min-height: 300px;">{{ old('syarat_ketentuan', $syaratKetentuan) }}</textarea>
                    @error('syarat_ketentuan')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Kebijakan Privasi Tab Content -->
            <div id="content-privasi" class="hidden">
                <div>
                    <div class="flex items-center justify-between mb-2 animate-on-scroll-zoom animate-on-scroll-delay-1">
                        <label for="kebijakan_privasi" class="block text-sm font-medium text-gray-300">
                            Kebijakan Privasi
                        </label>
                        <div class="text-xs text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Editor dengan tools lengkap tersedia
                        </div>
                    </div>
                    <textarea id="kebijakan_privasi" name="kebijakan_privasi" class="w-full bg-white border border-gray-300 rounded-lg text-gray-900 p-3 animate-on-scroll-zoom animate-on-scroll-delay-2" style="min-height: 300px;">{{ old('kebijakan_privasi', $kebijakanPrivasi) }}</textarea>
                    @error('kebijakan_privasi')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end animate-on-scroll-zoom animate-on-scroll-delay-1">
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-purple-500 hover:from-purple-700 hover:to-purple-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900 cursor-pointer">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
