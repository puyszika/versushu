<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // 🌐 Publikus: blog lista
    public function index(Request $request)
    {
        $search = trim($request->input('search'));

        $posts = Post::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(6);

        return view('blog.index', compact('posts', 'search'));
    }

    // 🌐 Publikus: egy bejegyzés megtekintése
    public function show(Post $post)
    {
        $post->load(['comments' => function ($q) {
            $q->whereNull('parent_id')->with('replies.user', 'user');
        }]);
        return view('blog.show', compact('post'));
    }

    // 👨‍💻 Admin: új poszt létrehozása
    public function create()
    {
        return view('admin.posts.create');
    }

    public function adminIndex()
    {
        $posts = Post::latest()->get();
        return view('admin.posts.index', compact('posts'));
    }

    // 👨‍💻 Admin: új poszt mentése
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $validated['user_id'] = Auth::id();

        Post::create($validated);

        return redirect()->route('blog.index')->with('success', 'Bejegyzés létrehozva!');
    }

    // 👨‍💻 Admin: szerkesztő űrlap
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    // 👨‍💻 Admin: poszt frissítése
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Régi képet töröljük
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($validated);

        return redirect()->route('admin.posts.index')->with('success', 'Bejegyzés frissítve!');
    }

    // 👨‍💻 Admin: poszt törlése
    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Bejegyzés törölve!');
    }



}
