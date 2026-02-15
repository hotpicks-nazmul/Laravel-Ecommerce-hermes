<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('author')->latest()->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'status' => 'required|in:published,draft',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => \Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'status' => $request->status,
            'author_id' => auth()->id(),
            'published_at' => $request->status === 'published' ? now() : null,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blogs', 'public');
            $data['image'] = Storage::url($path);
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully.');
    }

    public function show(Blog $blog)
    {
        return view('admin.blogs.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'status' => 'required|in:published,draft',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => \Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'status' => $request->status,
            'published_at' => $request->status === 'published' && !$blog->published_at ? now() : $blog->published_at,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($blog->image) {
                $oldPath = str_replace('/storage/', '', $blog->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image')->store('blogs', 'public');
            $data['image'] = Storage::url($path);
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        // Delete image from storage
        if ($blog->image) {
            $oldPath = str_replace('/storage/', '', $blog->image);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
        
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted successfully.');
    }
}
