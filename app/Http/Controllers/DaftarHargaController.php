<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DaftarHargaController extends Controller
{
    /**
     * Display the daftar harga page.
     */
    public function index(Request $request)
    {
        $query = Product::with(['game', 'icon'])->active();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('provider', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhereHas('game', function($gameQuery) use ($request) {
                      $gameQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        // Get products with pagination
        $entries = $request->get('entries', 25);
        $products = $query->orderBy('created_at', 'desc')->paginate($entries);
        
        // Handle AJAX requests
        if ($request->ajax()) {
            $html = view('daftar-harga.partials.table-rows', compact('products'))->render();
            $pagination = $products->appends($request->query())->links('vendor.pagination.tailwind')->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
        }
        
        return view('daftar-harga', compact('products'));
    }
}
