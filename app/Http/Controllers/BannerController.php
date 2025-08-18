<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;

class BannerController extends Controller
{
    public function index()
    {
        // Ambil daftar banner dari storage
        $banners = [];
        $bannerPath = 'banners';
        
        if (Storage::disk('public')->exists($bannerPath)) {
            $files = Storage::disk('public')->files($bannerPath);
            foreach ($files as $file) {
                $banners[] = [
                    'name' => basename($file),
                    'path' => asset('storage/' . $file),
                    'size' => Storage::disk('public')->size($file)
                ];
            }
        }
        
        return view('admin.banners', compact('banners'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ], [
                'banner.required' => 'Pilih file banner terlebih dahulu',
                'banner.image' => 'File harus berupa gambar',
                'banner.mimes' => 'Format file harus jpeg, png, jpg, atau gif',
                'banner.max' => 'Ukuran file maksimal 2MB'
            ]);

            if ($request->hasFile('banner')) {
                $file = $request->file('banner');
                
                // Simpan file ke storage dengan cara yang konsisten dengan GameController
                $path = $file->store('banners', 'public');
                
                return redirect()->route('admin.banners.index')
                    ->with('success', 'Banner berhasil ditambahkan');
            }

            return redirect()->route('admin.banners.index')
                ->with('error', 'Gagal menambahkan banner');
        } catch (\Exception $e) {
            return redirect()->route('admin.banners.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($filename)
    {
        try {
            $filePath = 'banners/' . $filename;
            
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                return redirect()->route('admin.banners.index')
                    ->with('success', 'Banner berhasil dihapus');
            }

            return redirect()->route('admin.banners.index')
                ->with('error', 'Banner tidak ditemukan');
        } catch (\Exception $e) {
            return redirect()->route('admin.banners.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
} 