<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Category;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;


class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Game::with(['category', 'target']);
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sub_judul', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%')
                  ->orWhereHas('category', function($categoryQuery) use ($request) {
                      $categoryQuery->where('name', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('target', function($targetQuery) use ($request) {
                      $targetQuery->where('judul_target', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        // Category filter
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }
        
        // Get games with pagination
        $games = $query->ordered()->paginate($request->get('entries', 25));
        
        // Get categories for filter dropdown
        $categories = Category::active()->ordered()->get();
        
        // If AJAX request, return JSON response
        if ($request->ajax()) {
            $html = view('admin.games.partials.table-rows', compact('games'))->render();
            $pagination = $games->appends($request->query())->links('vendor.pagination.tailwind')->toHtml();
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
        }
        
        return view('admin.games.index', compact('games', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        $targets = Target::where('is_active', true)->get();
        
        return view('admin.games.create', compact('categories', 'targets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'games' => 'required|string|max:255',
            'sub_judul' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games,slug',
            'kategori' => 'required|exists:categories,id',
            'sistem_target' => 'required|exists:targets,id',
            'deskripsi' => 'required|string',
            'status' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Handle file uploads
            $logoPath = $request->file('logo')->isValid() ? $request->file('logo')->store('games/logo', 'public') : null;
            $gambarPath = $request->file('gambar')->isValid() ? $request->file('gambar')->store('games/gambar', 'public') : null;
            $bannerPath = $request->file('banner')->isValid() ? $request->file('banner')->store('games/banner', 'public') : null;

            // Get the highest order value and add 1
            $maxOrder = Game::max('order') ?? 0;
            
            $game = Game::create([
                'name' => $request->games,
                'sub_judul' => $request->sub_judul,
                'slug' => $request->slug,
                'category_id' => $request->kategori,
                'target_id' => $request->sistem_target,
                'description' => $request->deskripsi,
                'logo' => $logoPath,
                'gambar' => $gambarPath,
                'banner' => $bannerPath,
                'order' => $maxOrder + 1,
                'is_active' => $request->status
            ]);

            return redirect()->route('admin.games.index')->with('success', 'Games berhasil ditambahkan');
        } catch (\Exception $e) {
            // Clean up uploaded files if error occurs
            if (isset($logoPath) && $logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            if (isset($gambarPath) && $gambarPath && Storage::disk('public')->exists($gambarPath)) {
                Storage::disk('public')->delete($gambarPath);
            }
            if (isset($bannerPath) && $bannerPath && Storage::disk('public')->exists($bannerPath)) {
                Storage::disk('public')->delete($bannerPath);
            }
            
            return back()->with('error', 'Gagal menambahkan games: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        return response()->json([
            'success' => true,
            'data' => $game->load('category')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        $categories = Category::active()->ordered()->get();
        $targets = Target::where('is_active', true)->get();
        
        return view('admin.games.edit', compact('game', 'categories', 'targets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'games' => 'required|string|max:255',
            'sub_judul' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games,slug,' . $game->id,
            'kategori' => 'required|exists:categories,id',
            'sistem_target' => 'required|exists:targets,id',
            'deskripsi' => 'required|string',
            'status' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $updateData = [
                'name' => $request->games,
                'sub_judul' => $request->sub_judul,
                'slug' => $request->slug,
                'category_id' => $request->kategori,
                'target_id' => $request->sistem_target,
                'description' => $request->deskripsi,
                'is_active' => $request->status
            ];

            // Handle file uploads if provided
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                // Delete old logo
                if ($game->logo && Storage::disk('public')->exists($game->logo)) {
                    Storage::disk('public')->delete($game->logo);
                }
                $updateData['logo'] = $request->file('logo')->store('games/logo', 'public');
            }

            if ($request->hasFile('gambar') && $request->file('gambar')->isValid()) {
                // Delete old gambar
                if ($game->gambar && Storage::disk('public')->exists($game->gambar)) {
                    Storage::disk('public')->delete($game->gambar);
                }
                $updateData['gambar'] = $request->file('gambar')->store('games/gambar', 'public');
            }

            if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                // Delete old banner
                if ($game->banner && Storage::disk('public')->exists($game->banner)) {
                    Storage::disk('public')->delete($game->banner);
                }
                $updateData['banner'] = $request->file('banner')->store('games/banner', 'public');
            }

            $game->update($updateData);

            return redirect()->route('admin.games.index')->with('success', 'Games berhasil diperbarui');
        } catch (\Exception $e) {
            // Clean up uploaded files if error occurs
            if (isset($updateData['logo']) && $updateData['logo'] && Storage::disk('public')->exists($updateData['logo'])) {
                Storage::disk('public')->delete($updateData['logo']);
            }
            if (isset($updateData['gambar']) && $updateData['gambar'] && Storage::disk('public')->exists($updateData['gambar'])) {
                Storage::disk('public')->delete($updateData['gambar']);
            }
            if (isset($updateData['banner']) && $updateData['banner'] && Storage::disk('public')->exists($updateData['banner'])) {
                Storage::disk('public')->delete($updateData['banner']);
            }
            
            return back()->with('error', 'Gagal memperbarui games: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        try {
            // Delete associated files
            if ($game->logo && Storage::disk('public')->exists($game->logo)) {
                Storage::disk('public')->delete($game->logo);
            }
            if ($game->gambar && Storage::disk('public')->exists($game->gambar)) {
                Storage::disk('public')->delete($game->gambar);
            }
            if ($game->banner && Storage::disk('public')->exists($game->banner)) {
                Storage::disk('public')->delete($game->banner);
            }

            $game->delete();

            return response()->json([
                'success' => true,
                'message' => 'Games berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus games',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the order of games.
     */
    public function updateOrder(Request $request)
    {
        try {
            $orders = $request->input('orders', []);
            
            foreach ($orders as $order) {
                if (isset($order['id']) && isset($order['order'])) {
                    Game::where('id', $order['id'])->update(['order' => $order['order']]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Urutan games berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan games',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 