<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $files = Storage::disk('public')->files('uploads');
        return view('admin.media.index', compact('files'));
    }

    public function upload(Request $request)
    {
        // Support both 'file' and 'image' field names (for different uploaders)
        $file = $request->file('file') ?? $request->file('image');
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded',
            ], 400);
        }
        
        // Validate file
        $request->validate([
            'file' => 'nullable|file|max:10240', // 10MB max
            'image' => 'nullable|image|max:10240', // 10MB max for images
        ]);

        // Store in blog-images folder for better organization
        $path = $file->store('blog-images', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::url($path),
        ]);
    }

    public function destroy($id)
    {
        // Delete file logic
        return response()->json(['success' => true]);
    }
}
