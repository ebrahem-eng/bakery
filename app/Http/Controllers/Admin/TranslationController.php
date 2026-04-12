<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TranslationController extends Controller
{
    protected $filePath;

    public function __construct()
    {
        $this->filePath = lang_path('ar.json');
    }

    public function index(Request $request)
    {
        if (!File::exists($this->filePath)) {
            File::put($this->filePath, json_encode([], JSON_UNESCAPED_UNICODE));
        }

        $translations = json_decode(File::get($this->filePath), true) ?: [];
        
        // Handle Search
        if ($request->filled('search')) {
            $search = mb_strtolower($request->search);
            $translations = array_filter($translations, function($val, $key) use ($search) {
                return mb_strpos(mb_strtolower($key), $search) !== false || 
                       mb_strpos(mb_strtolower($val), $search) !== false;
            }, ARRAY_FILTER_USE_BOTH);
        }

        // Handle Pagination
        $perPage = 50;
        $page = $request->input('page', 1);
        $offset = ($page * $perPage) - $perPage;
        
        $items = array_slice($translations, $offset, $perPage, true);
        
        $paginatedItems = new LengthAwarePaginator(
            $items,
            count($translations),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('Admin.Settings.translations', [
            'translations' => $paginatedItems,
            'search' => $request->search
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|string'
        ]);

        $translations = json_decode(File::get($this->filePath), true) ?: [];
        
        $translations[$request->key] = $request->value;

        File::put($this->filePath, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return response()->json([
            'success' => true,
            'message' => __('Translation updated successfully.')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|string'
        ]);

        $translations = json_decode(File::get($this->filePath), true) ?: [];
        
        if (isset($translations[$request->key])) {
             return back()->with('error', __('Key already exists.'));
        }

        $translations[$request->key] = $request->value;

        File::put($this->filePath, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return back()->with('success', __('New translation key added.'));
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'key' => 'required|string'
        ]);

        $translations = json_decode(File::get($this->filePath), true) ?: [];
        
        if (isset($translations[$request->key])) {
            unset($translations[$request->key]);
            File::put($this->filePath, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            
            return response()->json([
                'success' => true,
                'message' => __('Translation key deleted successfully.')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Key not found.')
        ], 404);
    }
}
