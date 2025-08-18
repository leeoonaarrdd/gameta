<?php

namespace App\Http\Controllers;

use App\Models\SocialMedia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SocialMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SocialMedia::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('icon', 'like', '%' . $request->search . '%')
                  ->orWhere('link', 'like', '%' . $request->search . '%');
            });
        }
        
        // Get social media with pagination
        $socialMedia = $query->orderBy('created_at', 'desc')->paginate($request->get('entries', 25));
        
        // Handle AJAX requests
        if ($request->ajax()) {
            $html = view('admin.social-media.partials.table-rows', compact('socialMedia'))->render();
            $pagination = $socialMedia->appends($request->query())->links('vendor.pagination.tailwind')->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
        }
        
        return view('admin.social-media', compact('socialMedia'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'icon' => 'required|string|max:255',
            'link' => 'required|url|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $socialMedia = SocialMedia::create([
                'icon' => $request->icon,
                'link' => $request->link
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sosial media berhasil ditambahkan',
                'data' => $socialMedia
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan sosial media',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SocialMedia $socialMedia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the social media by ID
        $socialMedia = SocialMedia::find($id);
        
        if (!$socialMedia) {
            return response()->json([
                'success' => false,
                'message' => 'Sosial media tidak ditemukan'
            ], 404);
        }
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'icon' => 'required|string|max:255',
            'link' => 'required|url|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $socialMedia->update([
                'icon' => $request->icon,
                'link' => $request->link
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sosial media berhasil diperbarui',
                'data' => $socialMedia
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui sosial media',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the social media by ID
        $socialMedia = SocialMedia::find($id);
        
        if (!$socialMedia) {
            return response()->json([
                'success' => false,
                'message' => 'Sosial media tidak ditemukan'
            ], 404);
        }
        
        try {
            $socialMedia->delete();
            return response()->json([
                'success' => true,
                'message' => 'Sosial media berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus sosial media',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
