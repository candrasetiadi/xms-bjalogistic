<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $q    = $request->get('q', '');
        $cat  = $request->get('category', '');
        $pub  = $request->get('status', '');

        $query = Post::orderByDesc('created_at');
        if ($q)   $query->where('title', 'like', "%$q%");
        if ($cat) $query->where('category', $cat);
        if ($pub === '1') $query->where('is_published', true);
        if ($pub === '0') $query->where('is_published', false);

        $posts      = $query->paginate(20)->withQueryString();
        $categories = Post::categories();

        return view('posts.index', compact('posts', 'q', 'cat', 'pub', 'categories'));
    }

    public function create()
    {
        return view('posts.form', ['post' => new Post(), 'categories' => Post::categories()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $request->filled('slug')
            ? Post::makeSlug($request->slug)
            : Post::makeSlug($request->title);

        Post::create($data);
        return redirect()->route('posts.index')->with('success', 'Artikel berhasil disimpan.');
    }

    public function edit(Post $post)
    {
        return view('posts.form', ['post' => $post, 'categories' => Post::categories()]);
    }

    public function update(Request $request, Post $post)
    {
        $data = $this->validated($request, $post->id);
        $data['slug'] = $request->filled('slug')
            ? Post::makeSlug($request->slug, $post->id)
            : Post::makeSlug($request->title, $post->id);

        $post->update($data);
        return redirect()->route('posts.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Artikel dihapus.');
    }

    // Public API — for website
    public function apiIndex(Request $request)
    {
        $posts = Post::where('is_published', true)
            ->when($request->category, fn($q, $c) => $q->where('category', $c))
            ->when($request->q, fn($q, $s) => $q->where('title', 'like', "%$s%"))
            ->orderByDesc('published_at')
            ->paginate(12);

        return response()->json($posts);
    }

    public function apiShow(string $slug)
    {
        $post = Post::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return response()->json($post);
    }

    private function validated(Request $request, ?int $postId = null): array
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'nullable|string',
        ]);

        $tags = array_values(array_filter(array_map('trim', explode(',', $request->tags_raw ?? ''))));

        return [
            'title'            => $request->title,
            'category'         => $request->category ?? 'Umum',
            'excerpt'          => $request->excerpt,
            'content'          => $request->content,
            'cover_url'        => $request->cover_url,
            'cover_alt'        => $request->cover_alt,
            'author'           => $request->author ?: 'Tim BJA Logistic',
            'published_at'     => $request->published_at ?: now()->toDateString(),
            'is_published'     => $request->boolean('is_published'),
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'focus_keyword'    => $request->focus_keyword,
            'tags'             => $tags ?: null,
            'og_title'         => $request->og_title,
            'og_image'         => $request->og_image,
            'og_description'   => $request->og_description,
        ];
    }
}
