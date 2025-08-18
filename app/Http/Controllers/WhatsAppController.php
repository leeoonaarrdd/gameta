<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;
use Illuminate\Routing\Controller;

class WhatsAppController extends Controller
{
    public function index()
    {
        // Ambil konfigurasi pesan WhatsApp dari database
        $topupNewMessage = Configuration::getValue('whatsapp_topup_new', '');
        $topupSuccessMessage = Configuration::getValue('whatsapp_topup_success', '');
        $newUserMessage = Configuration::getValue('whatsapp_new_user', '');
        $otpVerificationMessage = Configuration::getValue('whatsapp_otp_verification', '');
        $otpResetPasswordMessage = Configuration::getValue('whatsapp_otp_reset_password', '');

        return view('admin.whatsapp', compact(
            'topupNewMessage',
            'topupSuccessMessage',
            'newUserMessage',
            'otpVerificationMessage',
            'otpResetPasswordMessage'
        ));
    }

    public function update(Request $request)
    {
        $activeSection = $request->input('active_section', 'topup');
        
        if ($activeSection === 'topup') {
            $request->validate([
                'topup_new_message' => 'required|string',
                'topup_success_message' => 'required|string',
            ]);

            // Update konfigurasi pesan WhatsApp untuk topup
            Configuration::setValue('whatsapp_topup_new', $request->topup_new_message);
            Configuration::setValue('whatsapp_topup_success', $request->topup_success_message);

            return redirect()->back()->with('success_topup', 'Pesan Topup Saldo berhasil diperbarui!');
        } else {
            $request->validate([
                'new_user_message' => 'required|string',
                'otp_verification_message' => 'required|string',
                'otp_reset_password_message' => 'required|string',
            ]);

            // Update konfigurasi pesan WhatsApp untuk pesan lainnya
            Configuration::setValue('whatsapp_new_user', $request->new_user_message);
            Configuration::setValue('whatsapp_otp_verification', $request->otp_verification_message);
            Configuration::setValue('whatsapp_otp_reset_password', $request->otp_reset_password_message);

            return redirect()->back()->with('success_lainnya', 'Pesan Lainnya berhasil diperbarui!');
        }
    }


}
