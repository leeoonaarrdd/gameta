<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberOTP;
use App\Models\Configuration;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Routing\Controller;

class MemberAuthController extends Controller
{
    public function showLogin()
    {
        return view('member.login');
    }

    public function showRegister()
    {
        return view('member.register');
    }

    public function showAuth()
    {
        return view('member.auth');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('username', 'password');
        
        // Coba login dengan username
        if (Auth::guard('member')->attempt($credentials)) {
            $member = Auth::guard('member')->user();
            
            // Cek apakah member sudah terverifikasi phone
            if (!$member->phone_verified_at) {
                Auth::guard('member')->logout();
                return back()->withErrors([
                    'username' => 'Akun belum terverifikasi. Silakan verifikasi nomor telepon terlebih dahulu.',
                ])->withInput($request->only('username'));
            }
            
            // Update last login
            $member->update(['last_login_at' => now()]);
            
            $request->session()->regenerate();
            
            return redirect()->intended('/member/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:members,username',
            'email' => 'required|string|email|max:255|unique:members,email',
            'phone' => 'required|string|max:20|unique:members,phone',
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.unique' => 'Nomor telepon sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $member = Member::create([
            'name' => $request->username, // Gunakan username sebagai name
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active', // Set status active, tapi belum terverifikasi phone
            'balance' => 0,
        ]);

        // Generate OTP untuk verifikasi
        $otp = MemberOTP::createOTP($member->id, $member->phone, 'verification');

        // Ambil template pesan dari konfigurasi
        $messageTemplate = Configuration::getValue('whatsapp_otp_verification', 'Kode OTP verifikasi akun Anda adalah: #otp#. Berlaku selama 10 menit.');

        // Replace placeholder dengan nilai sebenarnya
        $message = str_replace('#otp#', $otp->otp_code, $messageTemplate);
        $message = str_replace('#username#', $member->username, $message);
        $message = str_replace('#name#', $member->name, $message);

        // Kirim OTP via WhatsApp
        try {
            $fonnteService = app(FonnteService::class);
            $result = $fonnteService->sendMessage($member->phone, $message);
            
            // Simpan member_id di session untuk verifikasi OTP
            session(['registration_member_id' => $member->id]);
            
            return redirect()->route('member.verify-registration-otp')->with('success', 'Registrasi berhasil! Kode OTP telah dikirim ke nomor WhatsApp Anda untuk verifikasi akun.');
            
        } catch (\Exception $e) {
            // Jika gagal kirim OTP, hapus member yang baru dibuat
            $member->delete();
            
            Log::error('Failed to send registration OTP', [
                'error' => $e->getMessage(),
                'member_id' => $member->id
            ]);
            
            return back()->withErrors(['phone' => 'Gagal mengirim OTP verifikasi. Silakan coba lagi.'])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }

    public function showResetPassword()
    {
        return view('member.reset-password');
    }

    public function resetPassword(Request $request, FonnteService $fonnteService)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|exists:members,username',
        ], [
            'username.required' => 'Username wajib diisi',
            'username.exists' => 'Username tidak ditemukan',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $member = Member::where('username', $request->username)->first();
            
            if (!$member) {
                return back()->withErrors(['username' => 'Username tidak ditemukan'])->withInput();
            }

            // Cek apakah member sudah verifikasi phone
            if (!$member->phone_verified_at) {
                return back()->withErrors(['username' => 'Akun belum terverifikasi. Silakan verifikasi nomor telepon terlebih dahulu.'])->withInput();
            }

            // Generate OTP
            $otp = MemberOTP::createOTP($member->id, $member->phone, 'reset_password');

            // Ambil template pesan dari konfigurasi
            $messageTemplate = Configuration::getValue('whatsapp_otp_reset_password', 'Kode OTP reset password Anda adalah: #otp#. Berlaku selama 10 menit.');

            // Replace placeholder dengan nilai sebenarnya
            $message = str_replace('#otp#', $otp->otp_code, $messageTemplate);
            $message = str_replace('#username#', $member->username, $message);
            $message = str_replace('#name#', $member->name, $message);

            // Kirim OTP via WhatsApp
            $result = $fonnteService->sendMessage($member->phone, $message);
            
            // Jika berhasil, simpan member_id di session untuk verifikasi OTP
            session(['reset_password_member_id' => $member->id]);
            
            Log::info('OTP sent successfully, redirecting to verify page', [
                'member_id' => $member->id,
                'phone' => $member->phone,
                'session_id' => session()->getId()
            ]);
            
            // Pastikan session disimpan sebelum redirect
            session()->save();
            
            return redirect()->route('member.verify-otp')->with('success', 'Kode OTP telah dikirim ke nomor WhatsApp Anda.');

        } catch (\Exception $e) {
            Log::error('Reset password error', [
                'error' => $e->getMessage(),
                'username' => $request->username
            ]);

            return back()->withErrors(['username' => 'Terjadi kesalahan. Silakan coba lagi.'])->withInput();
        }
    }

    public function showVerifyOTP()
    {
        if (!session('reset_password_member_id')) {
            return redirect()->route('member.reset-password');
        }

        return view('member.verify-otp');
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi',
            'otp.size' => 'Kode OTP harus 6 digit',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $memberId = session('reset_password_member_id');
        
        if (!$memberId) {
            return redirect()->route('member.reset-password');
        }

        try {
            $member = Member::find($memberId);
            
            if (!$member) {
                session()->forget('reset_password_member_id');
                return redirect()->route('member.reset-password')->withErrors(['otp' => 'Sesi tidak valid. Silakan coba lagi.']);
            }

            // Verifikasi OTP
            $isValid = MemberOTP::verifyOTP($memberId, $request->otp, 'reset_password');

            if ($isValid) {
                // Generate password baru
                $newPassword = $this->generateRandomPassword();
                
                // Update password member
                $member->update(['password' => Hash::make($newPassword)]);
                
                // Hapus session
                session()->forget('reset_password_member_id');
                
                return redirect()->route('member.login')->with('success', 'Password berhasil direset. Password baru Anda adalah: ' . $newPassword);
            } else {
                return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah expired.'])->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Verify OTP error', [
                'error' => $e->getMessage(),
                'member_id' => $memberId
            ]);

            return back()->withErrors(['otp' => 'Terjadi kesalahan. Silakan coba lagi.'])->withInput();
        }
    }

    private function generateRandomPassword($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $password;
    }

    public function showVerifyRegistrationOTP()
    {
        if (!session('registration_member_id')) {
            return redirect()->route('member.register');
        }

        return view('member.verify-registration-otp');
    }

    public function verifyRegistrationOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string|size:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi',
            'otp.size' => 'Kode OTP harus 6 digit',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $memberId = session('registration_member_id');
        
        if (!$memberId) {
            return redirect()->route('member.register');
        }

        try {
            $member = Member::find($memberId);
            
            if (!$member) {
                session()->forget('registration_member_id');
                return redirect()->route('member.register')->withErrors(['otp' => 'Sesi tidak valid. Silakan coba lagi.']);
            }

            // Verifikasi OTP
            $isValid = MemberOTP::verifyOTP($memberId, $request->otp, 'verification');

            if ($isValid) {
                // Set phone_verified_at untuk menandakan member sudah terverifikasi
                $member->update([
                    'phone_verified_at' => now()
                ]);
                
                // Kirim pesan ucapan selamat datang
                try {
                    $welcomeMessageTemplate = Configuration::getValue('whatsapp_new_user', 'Selamat datang di Gameta! Akun Anda telah berhasil diverifikasi. Selamat bermain!');
                    
                    // Replace placeholder dengan nilai sebenarnya
                    $welcomeMessage = str_replace('#username#', $member->username, $welcomeMessageTemplate);
                    $welcomeMessage = str_replace('#name#', $member->name, $welcomeMessage);
                    
                    $fonnteService = app(FonnteService::class);
                    $fonnteService->sendMessage($member->phone, $welcomeMessage);
                } catch (\Exception $e) {
                    // Jika gagal kirim pesan ucapan, tidak perlu error, hanya log saja
                    Log::error('Failed to send welcome message', [
                        'error' => $e->getMessage(),
                        'member_id' => $member->id
                    ]);
                }
                
                // Login member
                Auth::guard('member')->login($member);
                
                // Hapus session
                session()->forget('registration_member_id');
                
                return redirect('/member/dashboard')->with('success', 'Verifikasi berhasil! Selamat datang di Gameta.');
            } else {
                return back()->withErrors(['otp' => 'Kode OTP tidak valid atau sudah expired.'])->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Verify registration OTP error', [
                'error' => $e->getMessage(),
                'member_id' => $memberId
            ]);

            return back()->withErrors(['otp' => 'Terjadi kesalahan. Silakan coba lagi.'])->withInput();
        }
    }
}
