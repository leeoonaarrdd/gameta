<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Configuration;
use Illuminate\Routing\Controller;

class ConfigurationController extends Controller
{
    public function index()
    {
        // Ambil konfigurasi dari database
        $config = [
            'website_title' => Configuration::getValue('website_title', ''),
            'homepage_title' => Configuration::getValue('homepage_title', ''),
            'logo' => Configuration::getValue('logo', ''),
            'favicon' => Configuration::getValue('favicon', ''),
            'keywords' => Configuration::getValue('keywords', ''),
            'author' => Configuration::getValue('author', ''),
            'description' => Configuration::getValue('description', ''),
            'tripay_api_key' => Configuration::getValue('tripay_api_key', ''),
            'tripay_private_key' => Configuration::getValue('tripay_private_key', ''),
            'tripay_merchant_code' => Configuration::getValue('tripay_merchant_code', ''),
            'digiflazz_username' => Configuration::getValue('digiflazz_username', ''),
            'digiflazz_production_key' => Configuration::getValue('digiflazz_production_key', ''),
            'digiflazz_webhook_id' => Configuration::getValue('digiflazz_webhook_id', ''),
            'digiflazz_secret' => Configuration::getValue('digiflazz_secret', ''),
            'fonnte_token' => Configuration::getValue('fonnte_token', ''),
            // URL Callback tidak perlu disimpan di database karena sudah ditentukan oleh sistem
        ];

        return view('admin.configuration', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'website_title' => 'required|string|max:255',
            'homepage_title' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg|max:1024',
            'keywords' => 'required|string|max:500',
            'author' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('config', 'public');
            // Update config
            Configuration::setValue('logo', $logoPath);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('config', 'public');
            // Update config
            Configuration::setValue('favicon', $faviconPath);
        }

        // Update config values
        Configuration::setValue('website_title', $request->website_title);
        Configuration::setValue('homepage_title', $request->homepage_title);
        Configuration::setValue('keywords', $request->keywords);
        Configuration::setValue('author', $request->author);
        Configuration::setValue('description', $request->description);

        return redirect()->route('admin.configuration.index')
            ->with('success_umum', 'Konfigurasi berhasil diperbarui!')
            ->with('active_tab', 'umum');
    }

    public function updateTripay(Request $request)
    {
        $request->validate([
            'tripay_api_key' => 'required|string|max:255',
            'tripay_private_key' => 'required|string|max:255',
            'tripay_merchant_code' => 'required|string|max:255',
        ]);

        // Update Tripay config values
        Configuration::setValue('tripay_api_key', $request->tripay_api_key);
        Configuration::setValue('tripay_private_key', $request->tripay_private_key);
        Configuration::setValue('tripay_merchant_code', $request->tripay_merchant_code);
        // URL Callback tidak perlu disimpan karena sudah ditentukan oleh sistem

        return redirect()->route('admin.configuration.index')
            ->with('success_tripay', 'Konfigurasi Tripay berhasil diperbarui!')
            ->with('active_tab', 'tripay');
    }

    public function updateDigiflazz(Request $request)
    {
        $request->validate([
            'digiflazz_username' => 'required|string|max:255',
            'digiflazz_production_key' => 'required|string|max:255',
            'digiflazz_webhook_id' => 'nullable|string|max:255',
            'digiflazz_secret' => 'nullable|string|max:255',
        ]);

        // Update Digiflazz config values
        Configuration::setValue('digiflazz_username', $request->digiflazz_username);
        Configuration::setValue('digiflazz_production_key', $request->digiflazz_production_key);
        Configuration::setValue('digiflazz_webhook_id', $request->digiflazz_webhook_id ?? '');
        Configuration::setValue('digiflazz_secret', $request->digiflazz_secret ?? '');
        // URL Callback tidak perlu disimpan karena sudah ditentukan oleh sistem

        return redirect()->route('admin.configuration.index')
            ->with('success_digiflazz', 'Konfigurasi Digiflazz berhasil diperbarui!')
            ->with('active_tab', 'digiflazz');
    }

    public function updateFonnte(Request $request)
    {
        $request->validate([
            'fonnte_token' => 'required|string|max:500',
        ]);

        // Update Fonnte config values
        Configuration::setValue('fonnte_token', $request->fonnte_token);

        return redirect()->route('admin.configuration.index')
            ->with('success_fonnte', 'Konfigurasi Fonnte berhasil diperbarui!')
            ->with('active_tab', 'fonnte');
    }

    public function updateBantuan(Request $request)
    {
        $request->validate([
            'whatsapp_number' => 'required|string|max:20',
            'message_template' => 'required|string|max:1000',
        ]);

        // Update bantuan configuration
        Configuration::setValue('bantuan_whatsapp', $request->whatsapp_number);
        Configuration::setValue('bantuan_template', $request->message_template);

        return redirect()->route('admin.bantuan.index')
            ->with('success', 'Pengaturan halaman bantuan berhasil diperbarui!');
    }
}
