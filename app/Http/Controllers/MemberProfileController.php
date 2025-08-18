<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Member;
use Illuminate\Routing\Controller;

class MemberProfileController extends Controller
{
    public function showPengaturanAkun()
    {
        return view('member.pengaturan-akun');
    }

    public function updateProfile(Request $request)
    {
        $member = Auth::guard('member')->user();

        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:members,username,' . $member->id,
            'email' => 'required|email|max:255|unique:members,email,' . $member->id,
            'phone' => 'required|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|string|min:8|confirmed',
        ], [
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'phone.required' => 'No WhatsApp wajib diisi',
            'current_password.required_with' => 'Password lama wajib diisi jika ingin mengubah password',
            'new_password.min' => 'Password minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update data member
            $member->username = $request->username;
            $member->email = $request->email;
            $member->phone = $request->phone;

            // Update password jika diisi
            if ($request->filled('new_password')) {
                // Validasi password lama
                if (!Hash::check($request->current_password, $member->password)) {
                    return redirect()->back()
                        ->withErrors(['current_password' => 'Password lama tidak sesuai'])
                        ->withInput();
                }
                
                $member->password = Hash::make($request->new_password);
            }

            // Simpan perubahan ke database
            $member->save();

            return redirect()->back()->with('success', 'Profil berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui profil')
                ->withInput();
        }
    }
}
