<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Routing\Controller;

class PertanyaanUmumController extends Controller
{
    public function index()
    {
        // Ambil semua kategori FAQ
        $categories = FaqCategory::all();
        
        // Ambil semua FAQ
        $faqs = Faq::all();
        
        // Kelompokkan FAQ berdasarkan kategori
        $faqsByCategory = $faqs->groupBy('kategori');
        
        return view('pertanyaan-umum', compact('categories', 'faqsByCategory'));
    }
}
