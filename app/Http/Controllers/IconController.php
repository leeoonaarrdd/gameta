<?php

namespace App\Http\Controllers;

use App\Models\Icon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class IconController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $icons = Icon::orderBy('created_at', 'desc')->get();
        return view('admin.icons', compact('icons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ], [
            'name.required' => 'Nama icon harus diisi',
            'name.max' => 'Nama icon maksimal 255 karakter',
            'icon.required' => 'Pilih file icon terlebih dahulu',
            'icon.image' => 'File harus berupa gambar',
            'icon.mimes' => 'Format file harus jpeg, png, jpg, gif, atau svg',
            'icon.max' => 'Ukuran file maksimal 2MB'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            if ($request->hasFile('icon')) {
                $file = $request->file('icon');
                
                // Simpan file ke storage
                $path = $file->store('icons', 'public');
                
                // Simpan data ke database
                Icon::create([
                    'name' => $request->name,
                    'file_path' => $path,
                    'is_active' => true
                ]);
                
                return redirect()->route('admin.icons.index')
                    ->with('success', 'Icon berhasil ditambahkan');
            }

            return redirect()->route('admin.icons.index')
                ->with('error', 'Gagal menambahkan icon');
        } catch (\Exception $e) {
            return redirect()->route('admin.icons.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Icon $icon)
    {
        try {
            // Hapus file dari storage
            if (Storage::disk('public')->exists($icon->file_path)) {
                Storage::disk('public')->delete($icon->file_path);
            }
            
            // Hapus data dari database
            $icon->delete();
            
            return redirect()->route('admin.icons.index')
                ->with('success', 'Icon berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('admin.icons.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
