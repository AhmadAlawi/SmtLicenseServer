<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

/** Public blog — SEO content to give the sitemap real size and organic search something to index. */
class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()->published()->paginate(12);

        return view('blog.index', compact('posts'));
    }

    public function show(BlogPost $blogPost): View
    {
        return view('blog.show', ['post' => $blogPost]);
    }
}
