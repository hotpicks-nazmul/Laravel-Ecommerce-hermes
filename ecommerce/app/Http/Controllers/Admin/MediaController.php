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
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $path = $request->file('file')->store('uploads', 'public');

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
