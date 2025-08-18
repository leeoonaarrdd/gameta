<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $member = Auth::guard('member')->user();
        
        // Get member's purchases
        $purchases = Purchase::where('member_id', $member->id)->get();
        
        // Calculate statistics
        $totalOrders = $purchases->count();
        $totalSpent = $purchases->where('status', 'completed')->sum('total_amount');
        
        // Count by status
        $pendingCount = $purchases->where('status', 'pending')->count();
        $processingCount = $purchases->where('status', 'processing')->count();
        $successCount = $purchases->where('status', 'completed')->count();
        $cancelledCount = $purchases->where('status', 'cancelled')->count();
        
        return view('member.dashboard', compact(
            'totalOrders',
            'totalSpent',
            'pendingCount',
            'processingCount',
            'successCount',
            'cancelledCount'
        ));
    }
}
