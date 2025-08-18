<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }
        
        // Get admins with pagination
        $admins = $query->paginate($request->get('entries', 25));
        
        // If AJAX request, return JSON response
        if ($request->ajax()) {
            $html = view('admin.admins.partials.table-rows', compact('admins'))->render();
            $pagination = $admins->appends($request->query())->links('vendor.pagination.tailwind')->toHtml();
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ], 200, [], JSON_UNESCAPED_SLASHES);
        }
        
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log the request data
        Log::info('Admin store request data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_SLASHES);
        }

        try {
            // Debug: Log the data being saved
            $userData = [
                'name' => $request->name,
                'username' => $request->username,
                'status' => $request->status ?? 'active',
                'password' => Hash::make($request->password)
            ];
            Log::info('Creating admin with data:', $userData);
            
            $admin = User::create($userData);

            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil ditambahkan',
                'data' => $admin
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error('Admin creation error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan admin',
                'error' => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $admin)
    {
        return response()->json([
            'success' => true,
            'data' => $admin
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $admin)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $admin->id,
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:6',
            'password_confirmation' => 'nullable|same:password'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'name' => $request->name,
                'username' => $request->username,
                'status' => $request->status ?? $admin->status
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $admin->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil diperbarui',
                'data' => $admin
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui admin',
                'error' => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin)
    {
        try {
            // Prevent deleting self
            if (Auth::check() && $admin->id === Auth::id()) {
                            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun sendiri'
            ], 422, [], JSON_UNESCAPED_SLASHES);
            }

            $admin->delete();

            return response()->json([
                'success' => true,
                'message' => 'Admin berhasil dihapus'
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus admin',
                'error' => $e->getMessage()
            ], 500, [], JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('username', 'password');
        
        // Check if user exists and is active
        $user = User::where('username', $credentials['username'])->first();
        
        if (!$user || $user->status !== 'active') {
            return back()->with('error', 'Username atau password salah')->withInput();
        }

        // Attempt to authenticate
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            return redirect()->intended('/admin/dashboard');
        }

        return back()->with('error', 'Username atau password salah')->withInput();
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}
