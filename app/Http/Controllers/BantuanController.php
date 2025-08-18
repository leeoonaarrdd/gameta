<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class BantuanController extends Controller
{
    public function index()
    {
        return view('bantuan');
    }

    public function kirimPesan(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'wa' => 'required|string|max:20',
            'message' => 'required|string|max:1000',
        ]);

        try {
            // Ambil konfigurasi dari database
            $whatsappNumber = Configuration::getValue('bantuan_whatsapp', '085654008642');
            $messageTemplate = Configuration::getValue('bantuan_template', "Pesan baru dari #name#\nNo. Whatsapp: #wa#\nPesan: #message#");

            // Replace variabel dengan data user
            $message = str_replace(
                ['#name#', '#wa#', '#message#'],
                [$request->name, $request->wa, $request->message],
                $messageTemplate
            );

            // Format nomor WhatsApp menggunakan FonnteHelper
            $formattedNumber = \App\Helpers\FonnteHelper::formatPhone($whatsappNumber);

            // Kirim pesan menggunakan FonnteHelper
            $result = \App\Helpers\FonnteHelper::sendMessage($formattedNumber, $message);

            if ($result) {
                
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim! Tim kami akan segera menghubungi Anda.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim pesan. Silakan coba lagi.'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.'
            ], 500);
        }
    }


}
