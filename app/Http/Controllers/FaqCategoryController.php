<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaqCategory;
use Illuminate\Routing\Controller;

class FaqCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = FaqCategory::orderBy('name')->get();
        return view('admin.faq-categories', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:faq_categories,name',
        ]);

        FaqCategory::create([
            'name' => $request->name
        ]);

        return redirect()->route('admin.faq-categories.index')->with('success', 'Kategori FAQ berhasil ditambahkan!');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = FaqCategory::findOrFail($id);
        
        // Check if category is being used by any FAQ
        $faqCount = \App\Models\Faq::where('kategori', $category->name)->count();
        
        if ($faqCount > 0) {
            return redirect()->route('admin.faq-categories.index')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $faqCount . ' FAQ!');
        }
        
        $category->delete();

        return redirect()->route('admin.faq-categories.index')->with('success', 'Kategori FAQ berhasil dihapus!');
    }
}
