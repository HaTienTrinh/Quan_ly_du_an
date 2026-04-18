<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with('author');

        if ($request->filled('q')) {
            $term = $request->string('q')->trim();
            $query->where(function ($qry) use ($term) {
                $qry->where('title', 'like', '%'.$term.'%')
                    ->orWhere('slug', 'like', '%'.$term.'%')
                    ->orWhere('summary', 'like', '%'.$term.'%');
            });
        }

        if ($request->status === 'draft') {
            $query->where('status', 'draft');
        } elseif ($request->status === 'published') {
            $query->where('status', 'published');
        }

        $posts = $query->latest()->paginate(15)->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['author_id'] = $request->user()->id;

        if ($data['status'] === 'published') {
            $data['published_at'] = $data['published_at'] ?? now();
        } else {
            $data['published_at'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        } else {
            unset($data['thumbnail']);
        }

        Post::create($data);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Đã thêm bài viết thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load('author');

        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($data['status'] === 'published') {
            $data['published_at'] = $data['published_at'] ?? ($post->published_at ?? now());
        } else {
            $data['published_at'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail && ! Str::startsWith($post->thumbnail, ['http://', 'https://'])) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Đã cập nhật bài viết thành công.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Post $post)
    {
        if ($post->comments()->exists()) {
            return redirect()
                ->route('admin.posts.index')
                ->with('error', 'Không thể xóa bài viết đã có bình luận.');
        }

        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Đã chuyển bài viết vào thùng rác.');
    }

    public function trashed(Request $request)
    {
        $query = Post::onlyTrashed()->with('author');

        if ($request->filled('q')) {
            $search = $request->string('q')->trim();
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        $posts = $query->latest('deleted_at')->paginate(15)->withQueryString();

        return view('admin.posts.trashed', compact('posts'));
    }

    public function restore(int $id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $post->restore();

        return redirect()
            ->route('admin.posts.trashed')
            ->with('success', 'Đã khôi phục bài viết «'.$post->title.'».');
    }

    public function forceDestroy(int $id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);

        if ($post->comments()->exists()) {
            return redirect()
                ->route('admin.posts.trashed')
                ->with('error', 'Không thể xóa vĩnh viễn bài viết đã có bình luận.');
        }

        if ($post->thumbnail && ! Str::startsWith($post->thumbnail, ['http://', 'https://'])) {
            Storage::disk('public')->delete($post->thumbnail);
        }

        $post->forceDelete();

        return redirect()
            ->route('admin.posts.trashed')
            ->with('success', 'Đã xóa vĩnh viễn bài viết «'.$post->title.'».');
    }
}
