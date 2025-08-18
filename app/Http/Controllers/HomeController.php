<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use App\Models\Category;
use App\Models\Game;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil daftar banner dari storage
        $banners = [];
        $bannerPath = 'banners';
        
        if (Storage::disk('public')->exists($bannerPath)) {
            $files = Storage::disk('public')->files($bannerPath);
            foreach ($files as $file) {
                $banners[] = [
                    'name' => basename($file),
                    'path' => asset('storage/' . $file),
                    'size' => Storage::disk('public')->size($file)
                ];
            }
        }
        
        // Ambil data kategori yang aktif
        $categories = Category::active()->ordered()->with(['games' => function($query) {
            $query->active()->ordered();
        }])->get();
        
        // Ambil data games yang aktif untuk service section
        $games = Game::active()->ordered()->whereNotNull('logo')->get();
        
        return view('home', compact('banners', 'categories', 'games'));
    }
} 