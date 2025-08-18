<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Routing\Controller;

class CekPesananController extends Controller
{
    public function index()
    {
        // Ambil 10 pesanan terbaru dari database
        $latestOrders = Purchase::with(['product.game'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('cek-pesanan', compact('latestOrders'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string|max:255'
        ]);

        $orderId = $request->order_id;
        
        // Cari pesanan berdasarkan order_id
        $purchase = Purchase::with(['product.game', 'member'])
            ->where('order_id', 'like', "%{$orderId}%")
            ->first();

        if (!$purchase) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $purchase->order_id
            ]
        ]);
    }
}
