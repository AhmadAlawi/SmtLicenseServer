<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $posts = BlogPost::query()->published()->get(['slug', 'updated_at']);

        $urls = [
            ['loc' => route('home'), 'lastmod' => now()],
            ['loc' => route('foodics-alternative'), 'lastmod' => now()],
            ['loc' => route('vtech-alternative'), 'lastmod' => now()],
            ['loc' => route('signup.index'), 'lastmod' => now()],
            ['loc' => route('blog.index'), 'lastmod' => now()],
        ];

        foreach ($posts as $post) {
            $urls[] = ['loc' => route('blog.show', $post), 'lastmod' => $post->updated_at];
        }

        return response()
            ->view('sitemap.index', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
