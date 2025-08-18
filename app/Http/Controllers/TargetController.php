<?php

namespace App\Http\Controllers;

use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class TargetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Target::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('judul_target', 'like', '%' . $request->search . '%')
                  ->orWhere('teks_header', 'like', '%' . $request->search . '%');
            });
        }
        
        // Get targets with pagination
        $targets = $query->orderBy('created_at', 'desc')->paginate($request->get('entries', 25));
        
        // If AJAX request, return JSON response
        if ($request->ajax()) {
            $html = view('admin.targets.partials.table-rows', compact('targets'))->render();
            $pagination = $targets->appends($request->query())->links('vendor.pagination.tailwind')->toHtml();
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination
            ]);
        }
        
        return view('admin.targets.index', compact('targets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.targets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul_target' => 'required|string|max:255',
            'teks_header' => 'required|string|max:255',
            'konten' => 'required|string',
            'sparator' => 'nullable|string|max:10',
            'input_fields' => 'nullable|array',
            'option_fields' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process input fields
        $inputFields = [];
        if ($request->has('input_fields')) {
            foreach ($request->input_fields as $field) {
                if (!empty($field['judul_kolom'])) {
                    $inputFields[] = [
                        'judul_kolom' => $field['judul_kolom'],
                        'validasi' => $field['validasi'] ?? 'teks',
                    ];
                }
            }
        }

        // Process option fields
        $optionFields = [];
        if ($request->has('option_fields')) {
            foreach ($request->option_fields as $field) {
                if (!empty($field['pilihan'])) {
                    $pilihanData = [];
                    
                    // Check if we have pilihan_data (hidden input with JSON data)
                    if (isset($field['pilihan_data']) && !empty($field['pilihan_data'])) {
                        try {
                            $pilihanData = json_decode($field['pilihan_data'], true) ?: [];
                        } catch (\Exception $e) {
                            $pilihanData = [];
                        }
                    }
                    
                    $optionFields[] = [
                        'judul_kolom' => $field['pilihan'], // Simpan judul kolom yang ditulis user
                        'pilihan' => $pilihanData,
                    ];
                }
            }
        }

        Target::create([
            'judul_target' => $request->judul_target,
            'teks_header' => $request->teks_header,
            'konten' => $request->konten,
            'sparator' => $request->sparator,
            'input_fields' => $inputFields,
            'option_fields' => $optionFields,
        ]);

        return redirect()->route('admin.targets.index')
            ->with('success', 'Target berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Target $target)
    {
        return view('admin.targets.edit', compact('target'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Target $target)
    {
        $validator = Validator::make($request->all(), [
            'judul_target' => 'required|string|max:255',
            'teks_header' => 'required|string|max:255',
            'konten' => 'required|string',
            'sparator' => 'nullable|string|max:10',
            'input_fields' => 'nullable|array',
            'option_fields' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process input fields
        $inputFields = [];
        if ($request->has('input_fields')) {
            foreach ($request->input_fields as $field) {
                if (!empty($field['judul_kolom'])) {
                    $inputFields[] = [
                        'judul_kolom' => $field['judul_kolom'],
                        'validasi' => $field['validasi'] ?? 'teks',
                    ];
                }
            }
        }

        // Process option fields
        $optionFields = [];
        if ($request->has('option_fields')) {
            foreach ($request->option_fields as $field) {
                if (!empty($field['pilihan'])) {
                    $pilihanData = [];
                    
                    // Check if we have pilihan_data (hidden input with JSON data)
                    if (isset($field['pilihan_data']) && !empty($field['pilihan_data'])) {
                        try {
                            $pilihanData = json_decode($field['pilihan_data'], true) ?: [];
                        } catch (\Exception $e) {
                            $pilihanData = [];
                        }
                    }
                    
                    $optionFields[] = [
                        'judul_kolom' => $field['pilihan'], // Simpan judul kolom yang ditulis user
                        'pilihan' => $pilihanData,
                    ];
                }
            }
        }

        $target->update([
            'judul_target' => $request->judul_target,
            'teks_header' => $request->teks_header,
            'konten' => $request->konten,
            'sparator' => $request->sparator,
            'input_fields' => $inputFields,
            'option_fields' => $optionFields,
        ]);

        return redirect()->route('admin.targets.index')
            ->with('success', 'Target berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Target $target)
    {
        try {
            $target->delete();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Target berhasil dihapus!'
                ]);
            }
            
            return redirect()->route('admin.targets.index')
                ->with('success', 'Target berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus target: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.targets.index')
                ->with('error', 'Gagal menghapus target: ' . $e->getMessage());
        }
    }
} 