<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\Configuration;
use App\Helpers\FonnteHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;

class TopupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Topup::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('topup_id', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sort by tanggal (newest first)
        $query->orderBy('tanggal', 'desc');
        
        // Get topups with pagination
        $topups = $query->paginate($request->get('entries', 25));
        
        // If AJAX request, return JSON response
        if ($request->ajax()) {
            $html = view('admin.topups.partials.table-rows', compact('topups'))->render();
            $pagination = $topups->appends($request->query())->links('vendor.pagination.tailwind')->toHtml();
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
        }
        
        return view('admin.topups', compact('topups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.topups');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'topup_id' => 'required|string|max:255|unique:topups,topup_id',
            'jumlah' => 'required|numeric|min:0',
            'status' => 'required|in:pending,success,failed,cancelled'
        ]);

        $topup = Topup::create($request->all());

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Topup berhasil ditambahkan',
                'data' => $topup
            ]);
        }

        return redirect()->route('admin.topups.index')
            ->with('success', 'Topup berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Topup $topup)
    {
        if (request()->ajax()) {
            // Return complete topup data including payment information
            return response()->json([
                'id' => $topup->id,
                'username' => $topup->username,
                'topup_id' => $topup->topup_id,
                'jumlah' => $topup->jumlah,
                'status' => $topup->status,
                'payment_provider' => $topup->payment_provider,
                'payment_method' => $topup->payment_method,
                'payment_account' => $topup->payment_account,
                'payment_name' => $topup->payment_name,
                'payment_code' => $topup->payment_code,
                'payment_category' => $topup->payment_category,
                'payment_notes' => $topup->payment_notes,
                'member_id' => $topup->member_id,
                'tanggal' => $topup->tanggal,
                'created_at' => $topup->created_at,
                'updated_at' => $topup->updated_at
            ]);
        }
        return view('admin.topups', compact('topup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Topup $topup)
    {
        if (request()->ajax()) {
            // Return complete topup data including payment information
            return response()->json([
                'id' => $topup->id,
                'username' => $topup->username,
                'topup_id' => $topup->topup_id,
                'jumlah' => $topup->jumlah,
                'status' => $topup->status,
                'payment_provider' => $topup->payment_provider,
                'payment_method' => $topup->payment_method,
                'payment_account' => $topup->payment_account,
                'payment_name' => $topup->payment_name,
                'payment_code' => $topup->payment_code,
                'payment_category' => $topup->payment_category,
                'payment_notes' => $topup->payment_notes,
                'member_id' => $topup->member_id,
                'tanggal' => $topup->tanggal,
                'created_at' => $topup->created_at,
                'updated_at' => $topup->updated_at
            ]);
        }
        return view('admin.topups', compact('topup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Topup $topup)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'topup_id' => 'required|string|max:255|unique:topups,topup_id,' . $topup->id,
            'jumlah' => 'required|numeric|min:0',
            'status' => 'required|in:pending,success,failed,cancelled'
        ]);

        $topup->update($request->all());

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Topup berhasil diperbarui',
                'data' => $topup
            ]);
        }

        return redirect()->route('admin.topups.index')
            ->with('success', 'Topup berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topup $topup)
    {
        $topup->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Topup berhasil dihapus'
            ]);
        }

        return redirect()->route('admin.topups.index')
            ->with('success', 'Topup berhasil dihapus');
    }

    /**
     * Update topup configuration
     */
    public function updateConfig(Request $request)
    {
        $request->validate([
            'topup_prefix' => 'required|string|max:10',
            'topup_invoice_duration' => 'required|numeric|min:1|max:1440',
        ]);

        try {
            // Update configuration values
            \App\Models\Configuration::setValue('topup_prefix', $request->topup_prefix);
            \App\Models\Configuration::setValue('topup_invoice_duration', $request->topup_invoice_duration);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Konfigurasi topup berhasil diperbarui'
                ]);
            }

            return redirect()->route('admin.topups.index')
                ->with('success', 'Konfigurasi topup berhasil diperbarui');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui konfigurasi topup'
                ], 500);
            }

            return redirect()->route('admin.topups.index')
                ->with('error', 'Gagal memperbarui konfigurasi topup');
        }
    }

    /**
     * Accept topup payment (for manual payment methods)
     */
    public function acceptTopup(Topup $topup)
    {
        try {
            // Check if topup is pending
            if ($topup->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Topup hanya dapat diterima jika status pending'
                ], 400);
            }

            // Check if payment provider is manual (case insensitive)
            if (strtolower($topup->payment_provider) !== 'manual') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tombol terima hanya tersedia untuk metode pembayaran manual'
                ], 400);
            }

            // Update topup status to success
            $topup->update(['status' => 'success']);

            // Add balance to member if member_id exists
            if ($topup->member_id) {
                $member = \App\Models\Member::find($topup->member_id);
                if ($member) {
                    $member->increment('balance', $topup->jumlah);
                    
                    // Kirim notifikasi WhatsApp untuk topup berhasil
                    if (FonnteHelper::isConfigured() && $member->phone) {
                        try {
                            // Refresh member data untuk mendapatkan balance terbaru
                            $member->refresh();
                            
                            $topupData = [
                                'username' => $member->username,
                                'topup_id' => $topup->topup_id,
                                'amount' => $topup->jumlah,
                                'balance' => $member->balance
                            ];
                            
                            FonnteHelper::sendTopupSuccessNotification($member->phone, $topupData);
                        } catch (\Exception $e) {
                            Log::error('Failed to send topup success WhatsApp notification', [
                                'error' => $e->getMessage(),
                                'member_id' => $member->id,
                                'topup_id' => $topup->topup_id
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Topup berhasil diterima dan saldo member telah ditambahkan',
                'data' => $topup
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menerima topup: ' . $e->getMessage()
            ], 500);
        }
    }
}
