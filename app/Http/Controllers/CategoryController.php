<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Get categories with pagination and games count
        $categories = $query->ordered()->withCount('games')->paginate($request->get('entries', 25));
        
        // If AJAX request, return JSON response
        if ($request->ajax()) {
            $html = view('admin.categories.partials.table-rows', compact('categories'))->render();
            $pagination = $categories->appends($request->query())->links('vendor.pagination.tailwind')->toHtml();
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
        }
        
        return view('admin.categories', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $maxOrder = Category::max('order') ?? 0;
            $category = Category::create([
                'name' => $request->name,
                'order' => $request->order ?? $maxOrder + 1,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'data' => $category->load('games')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category->update([
                'name' => $request->name,
                'order' => $request->order ?? $category->order,
                'is_active' => $request->has('is_active') ? $request->is_active : $category->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kategori',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            // Check if category has games
            if ($category->games()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus kategori yang memiliki games'
                ], 422);
            }

            $category->delete();

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

    /**
     * Get categories for dropdown/select
     */
    public function getCategories()
    {
        $categories = Category::active()->ordered()->get(['id', 'name']);
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Update order of categories
     */
    public function updateOrder(Request $request)
    {
        try {
            $orders = $request->input('orders', []);
            foreach ($orders as $order) {
                if (isset($order['id']) && isset($order['order'])) {
                    Category::where('id', $order['id'])->update(['order' => $order['order']]);
                }
            }
            return response()->json(['success' => true, 'message' => 'Urutan kategori berhasil diperbarui']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui urutan kategori', 'error' => $e->getMessage()], 500);
        }
    }
} 