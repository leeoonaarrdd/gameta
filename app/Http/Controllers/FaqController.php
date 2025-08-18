<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use Illuminate\Routing\Controller;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Faq::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kategori', 'like', "%{$search}%")
                  ->orWhere('pertanyaan', 'like', "%{$search}%")
                  ->orWhere('konten', 'like', "%{$search}%");
            });
        }
        
        // Get entries per page
        $entries = $request->get('entries', 25);
        
        // Order by
        $query->orderBy('kategori')->orderBy('created_at', 'desc');
        
        // Paginate
        $faqs = $query->paginate($entries);
        
        // Handle AJAX requests
        if ($request->ajax()) {
            $html = view('admin.faqs.partials.table-rows', compact('faqs'))->render();
            $pagination = $faqs->appends($request->query())->links('vendor.pagination.tailwind')->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
        }
        
        return view('admin.faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\FaqCategory::orderBy('name')->get(['id', 'name']);
        return view('admin.faqs.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
            'pertanyaan' => 'required|string',
            'konten' => 'required|string',
        ]);

        Faq::create([
            'kategori' => $request->kategori,
            'pertanyaan' => $request->pertanyaan,
            'konten' => $request->konten,
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'Pertanyaan umum berhasil ditambahkan!');
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $faq = Faq::findOrFail($id);
        $categories = \App\Models\FaqCategory::orderBy('name')->get(['id', 'name']);
        return view('admin.faqs.edit', compact('faq', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kategori' => 'required|string|max:255',
            'pertanyaan' => 'required|string',
            'konten' => 'required|string',
        ]);

        $faq = Faq::findOrFail($id);
        $faq->update([
            'kategori' => $request->kategori,
            'pertanyaan' => $request->pertanyaan,
            'konten' => $request->konten,
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'Pertanyaan umum berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan umum berhasil dihapus!'
            ]);
        }

        return redirect()->route('admin.faqs.index')->with('success', 'Pertanyaan umum berhasil dihapus!');
    }
}
