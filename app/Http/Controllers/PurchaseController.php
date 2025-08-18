<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Routing\Controller;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['user', 'product'])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('username', 'like', "%{$search}%")
                               ->orWhere('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $entries = $request->get('entries', 25);
        $purchases = $query->paginate($entries);

        // If AJAX request, return JSON response
        if ($request->ajax()) {
            $html = view('admin.purchases.partials.table-rows', compact('purchases'))->render();
            $pagination = $purchases->appends($request->query())->links('vendor.pagination.tailwind')->toHtml();
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
        }

        return view('admin.purchases.index', compact('purchases'));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['user', 'product']);
        return view('admin.purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $purchase->load(['user', 'product']);
        return view('admin.purchases.edit', compact('purchase'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'payment_method' => 'required|string|max:255',
            'player_id' => 'nullable|string|max:255',
            'player_nickname' => 'nullable|string|max:255',
            'admin_fee' => 'nullable|numeric|min:0',
            'unique_code' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,cancelled,failed',
            'notes' => 'nullable|string'
        ]);

        // Update purchase data
        $purchase->update([
            'product_id' => $request->product_id,
            'payment_method' => $request->payment_method,
            'total_amount' => $request->total_amount,
            'status' => $request->status
        ]);

        // Update notes (JSON data)
        $notes = json_decode($purchase->notes, true) ?? [];
        $notes['player_id'] = $request->player_id;
        $notes['player_nickname'] = $request->player_nickname;
        $notes['admin_fee'] = $request->admin_fee;
        $notes['unique_code'] = $request->unique_code;
        
        $purchase->update(['notes' => json_encode($notes)]);

        return redirect()->route('admin.purchases.index')->with('success', 'Pembelian berhasil diperbarui');
    }

    public function destroy(Purchase $purchase)
    {
        try {
            $purchase->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembelian berhasil dihapus'
                ]);
            }
            
            return redirect()->route('admin.purchases.index')->with('success', 'Pembelian berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus pembelian'
                ], 500);
            }
            
            return redirect()->route('admin.purchases.index')->with('error', 'Gagal menghapus pembelian');
        }
    }

    public function updateStatus(Request $request, Purchase $purchase)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,failed'
        ]);

        $purchase->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Status pembelian berhasil diperbarui');
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'order_prefix' => 'required|string|max:10',
            'invoice_duration' => 'required|integer|min:1|max:1440'
        ]);

        try {
            // Update configuration
            \App\Models\Configuration::setValue('order_prefix', $request->order_prefix);
            \App\Models\Configuration::setValue('invoice_duration', $request->invoice_duration);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Konfigurasi pembelian berhasil diperbarui'
                ]);
            }

            return redirect()->back()->with('success', 'Konfigurasi pembelian berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan konfigurasi: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan konfigurasi');
        }
    }
}
