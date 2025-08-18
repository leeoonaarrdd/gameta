<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethodCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PaymentMethodCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = PaymentMethodCategory::orderBy('name', 'asc')->get();
        return view('admin.payment-method-categories', compact('categories'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:payment_method_categories,name',
        ]);

        PaymentMethodCategory::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.payment-method-categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethodCategory $paymentMethodCategory)
    {
        return response()->json($paymentMethodCategory);
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethodCategory $paymentMethodCategory)
    {
        try {
            // Check if category is used by any payment methods
            if ($paymentMethodCategory->paymentMethods()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena masih memiliki metode pembayaran'
                ], 400);
            }
            
            $paymentMethodCategory->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
