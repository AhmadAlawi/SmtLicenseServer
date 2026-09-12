<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()->latest()->get();

        return view('dashboard.blog-posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('dashboard.blog-posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        BlogPost::query()->create($data);

        return redirect()->route('dashboard.blog-posts.index')->with('status', "Post \"{$data['title']}\" created.");
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('dashboard.blog-posts.edit', ['post' => $blogPost]);
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $this->validated($request, $blogPost);

        $blogPost->update($data);

        return redirect()->route('dashboard.blog-posts.index')->with('status', "Post \"{$blogPost->title}\" updated.");
    }

    public function togglePublished(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->update(['is_published' => ! $blogPost->is_published]);

        $state = $blogPost->is_published ? 'published' : 'unpublished';

        return back()->with('status', "Post \"{$blogPost->title}\" {$state}.");
    }

    /** @return array<string,mixed> */
    private function validated(Request $request, ?BlogPost $post = null): array
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('blog_posts', 'slug')->ignore($post)],
            'excerpt'          => ['required', 'string', 'max:500'],
            'body'             => ['required', 'string'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ]);

        $data['is_published'] = $request->boolean('is_published', true);
        $data['published_at'] = $post?->published_at ?? now();

        return $data;
    }
}
