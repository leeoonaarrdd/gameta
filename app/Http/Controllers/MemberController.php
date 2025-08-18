<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class MemberController extends Controller
{
    /**
     * Display member dashboard
     */
    public function dashboard()
    {
        $member = auth()->guard('member')->user();
        
        // Cek apakah member sudah terverifikasi
        if (!$member->phone_verified_at) {
            return redirect()->route('member.otp.verification', $member->id)
                ->with('error', 'Silakan verifikasi nomor WhatsApp Anda terlebih dahulu.');
        }
        
        return view('member.dashboard', compact('member'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Member::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // Get members with pagination
        $members = $query->orderBy('created_at', 'desc')->paginate($request->get('entries', 25));
        
        // If AJAX request, return JSON response
        if ($request->ajax()) {
            $html = view('admin.members.partials.table-rows', compact('members'))->render();
            $pagination = $members->appends($request->query())->links('vendor.pagination.tailwind')->toHtml();
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
        }
        
        return view('admin.members', compact('members'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        return response()->json($member);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        return response()->json($member);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Member $member)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:members,username,' . $member->id,
            'phone' => 'nullable|string|max:20',
            'balance' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'verification_status' => 'required|in:verified,unverified'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle verification status
            $phoneVerifiedAt = null;
            if ($request->verification_status === 'verified') {
                // If member was not verified before, set current timestamp
                // If member was already verified, keep the original timestamp
                $phoneVerifiedAt = $member->phone_verified_at ?? now();
            }

            $member->update([
                'username' => $request->username,
                'phone' => $request->phone,
                'balance' => $request->balance ?? 0,
                'status' => $request->status,
                'phone_verified_at' => $phoneVerifiedAt
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Member berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui member',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle member status (active/inactive)
     */
    public function toggleStatus(Request $request, Member $member)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Status tidak valid'
            ], 422);
        }

        try {
            $member->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Status member berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status member',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        try {
            $member->delete();

            return response()->json([
                'success' => true,
                'message' => 'Member berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus member',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
