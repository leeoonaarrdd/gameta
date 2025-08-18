<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class ContentController extends Controller
{
    public function index()
    {
        $syaratKetentuan = Configuration::getValue('syarat_ketentuan', '');
        $kebijakanPrivasi = Configuration::getValue('kebijakan_privasi', '');
        
        return view('admin.content', compact('syaratKetentuan', 'kebijakanPrivasi'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'syarat_ketentuan' => 'nullable|string',
            'kebijakan_privasi' => 'nullable|string',
        ]);

        try {
            // Save only if data is provided
            if ($request->filled('syarat_ketentuan')) {
                Configuration::setValue('syarat_ketentuan', $request->syarat_ketentuan);
            }
            
            if ($request->filled('kebijakan_privasi')) {
                Configuration::setValue('kebijakan_privasi', $request->kebijakan_privasi);
            }
            
            return redirect()->route('admin.content.index')
                ->with('success', 'Konten halaman berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->route('admin.content.index')
                ->with('error', 'Gagal menyimpan konten: ' . $e->getMessage());
        }
    }
}
