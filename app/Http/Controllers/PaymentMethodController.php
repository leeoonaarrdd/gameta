<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\PaymentMethodCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PaymentMethod::query();
        
        // Exclude Innerpay payment method from admin management
        $query->where(function($q) {
            $q->where('name', 'not like', '%innerpay%')
              ->where('name', 'not like', '%InnerPay%')
              ->where('provider', 'not like', '%innerpay%')
              ->where('provider', 'not like', '%InnerPay%');
        });
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('provider', 'like', '%' . $request->search . '%')
                  ->orWhere('unique_code', 'like', '%' . $request->search . '%')
                  ->orWhere('kategori', 'like', '%' . $request->search . '%');
            });
        }
        
        // Get payment methods with pagination
        $paymentMethods = $query->orderBy('name', 'asc')->paginate($request->get('entries', 25));
        
        // Handle AJAX requests
        if ($request->ajax()) {
            $html = view('admin.payment-methods.partials.table-rows', compact('paymentMethods'))->render();
            $pagination = $paymentMethods->appends($request->query())->links('vendor.pagination.tailwind')->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
        }
        
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = PaymentMethodCategory::orderBy('name', 'asc')->get();
        return view('admin.payment-methods.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Prevent creating Innerpay payment method
        if (strtolower($request->name) === 'innerpay' || 
            strtolower($request->provider) === 'innerpay' ||
            strpos(strtolower($request->name), 'innerpay') !== false ||
            strpos(strtolower($request->provider), 'innerpay') !== false) {
            return redirect()->route('admin.payment-methods.create')
                ->with('error', 'Tidak dapat membuat metode pembayaran dengan nama Innerpay')
                ->withInput();
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'method_code' => 'required|string|max:255',
            'admin_fee' => 'nullable|integer|min:0|max:99999999',
            'admin_fee_percentage' => 'nullable|integer|min:0|max:100',
            'has_unique_code' => 'required|boolean',
            'is_active' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'kategori' => $request->kategori,
            'provider' => $request->provider,
            'method_code' => $request->method_code,
            'admin_fee' => $request->admin_fee ?: null,
            'admin_fee_percentage' => $request->admin_fee_percentage ?: null,
            'has_unique_code' => $request->has_unique_code,
            'is_active' => $request->is_active,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('payment-methods', 'public');
            $data['image'] = $imagePath;
        }

        PaymentMethod::create($data);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        return response()->json($paymentMethod);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        // Prevent editing Innerpay payment method
        if ($this->isInnerpayMethod($paymentMethod)) {
            return redirect()->route('admin.payment-methods.index')
                ->with('error', 'Metode pembayaran Innerpay tidak dapat diedit');
        }
        
        $categories = PaymentMethodCategory::orderBy('name', 'asc')->get();
        return view('admin.payment-methods.edit', compact('paymentMethod', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        // Prevent updating Innerpay payment method
        if ($this->isInnerpayMethod($paymentMethod)) {
            return redirect()->route('admin.payment-methods.index')
                ->with('error', 'Metode pembayaran Innerpay tidak dapat diperbarui');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'method_code' => 'required|string|max:255',
            'admin_fee' => 'nullable|numeric|min:0|max:99999999.99',
            'admin_fee_percentage' => 'nullable|numeric|min:0|max:100',
            'has_unique_code' => 'required|boolean',
            'is_active' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'kategori' => $request->kategori,
            'provider' => $request->provider,
            'method_code' => $request->method_code,
            'admin_fee' => $request->admin_fee ?: null,
            'admin_fee_percentage' => $request->admin_fee_percentage ?: null,
            'has_unique_code' => $request->has_unique_code,
            'is_active' => $request->is_active,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($paymentMethod->image) {
                Storage::disk('public')->delete($paymentMethod->image);
            }
            
            $imagePath = $request->file('image')->store('payment-methods', 'public');
            $data['image'] = $imagePath;
        }

        $paymentMethod->update($data);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        // Prevent deleting Innerpay payment method
        if ($this->isInnerpayMethod($paymentMethod)) {
            return response()->json([
                'success' => false,
                'message' => 'Metode pembayaran Innerpay tidak dapat dihapus'
            ], 403);
        }
        
        try {
            // Delete image if exists
            if ($paymentMethod->image) {
                Storage::disk('public')->delete($paymentMethod->image);
            }
            
            $paymentMethod->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Metode pembayaran berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus metode pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check if payment method is Innerpay
     */
    private function isInnerpayMethod($paymentMethod)
    {
        return strtolower($paymentMethod->name) === 'innerpay' ||
               strtolower($paymentMethod->provider) === 'innerpay' ||
               strpos(strtolower($paymentMethod->name), 'innerpay') !== false ||
               strpos(strtolower($paymentMethod->provider), 'innerpay') !== false;
    }
}
