<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SearchController extends Controller
{
    /**
     * Search games and categories
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => [
                    'games' => [],
                    'categories' => []
                ]
            ]);
        }

        // Search games
        $games = Game::where('name', 'LIKE', "%{$query}%")
            ->orWhere('sub_judul', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->with('category')
            ->limit(10)
            ->get()
            ->map(function ($game) {
                return [
                    'id' => $game->id,
                    'name' => $game->name,
                    'sub_judul' => $game->sub_judul,
                    'slug' => $game->slug,
                    'gambar' => $game->gambar ? asset('storage/' . $game->gambar) : null,
                    'category' => $game->category ? $game->category->name : null,
                    'type' => 'game',
                    'url' => route('checkout.show', $game->slug)
                ];
            });

        // Search categories
        $categories = Category::where('name', 'LIKE', "%{$query}%")
            ->with(['games' => function ($query) {
                $query->limit(5);
            }])
            ->limit(5)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'game_count' => $category->games->count(),
                    'type' => 'category',
                    'games' => $category->games->map(function ($game) {
                        return [
                            'id' => $game->id,
                            'name' => $game->name,
                            'slug' => $game->slug,
                            'gambar' => $game->gambar ? asset('storage/' . $game->gambar) : null,
                            'url' => route('checkout.show', $game->slug)
                        ];
                    })
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'games' => $games,
                'categories' => $categories
            ]
        ]);
    }
}
