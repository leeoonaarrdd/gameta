<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class MemberPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $member = Auth::guard('member')->user();
        
        $query = Purchase::with(['product.game'])
            ->where('member_id', $member->id)
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $entries = $request->get('entries', 10);
        $purchases = $query->paginate($entries);

        return view('member.pesanan-saya', compact('purchases'));
    }

}
