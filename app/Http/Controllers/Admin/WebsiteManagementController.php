<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WebsiteManagementController extends Controller
{
    public function index()
    {
        return view('Admin.Website.index');
    }

    public function update(Request $request)
    {
        $settings = $request->except(['_token', 'images']);

        foreach ($settings as $key => $value) {
            // Handle booleans for visibility
            if (str_ends_with($key, '_visible')) {
                $value = $value === 'on' || $value === '1' || $value === true ? '1' : '0';
            }
            Setting::set($key, $value);
        }

        return response()->json([
            'success' => true,
            'message' => __('Website content updated successfully.')
        ]);
    }

    public function updateImage(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        $key = $request->key;
        $image = $request->file('image');

        // Delete old image if exists
        $oldPath = Setting::get($key);
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // Store new image
        $path = $image->store('website', 'public');
        Setting::set($key, $path);

        return response()->json([
            'success' => true,
            'message' => __('Image updated successfully.'),
            'path' => asset('storage/' . $path)
        ]);
    }
}
