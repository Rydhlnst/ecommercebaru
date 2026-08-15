<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogFrontendController extends Controller
{
    /**
     * Display a listing of published blog posts.
     */
    public function index(Request $request): View
    {
        $categories = BlogCategory::withCount(['posts' => function ($q) {
            $q->where('is_published', true);
        }])->get();

        $query = BlogPost::with('category')
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->latest();

        if ($categorySlug = $request->get('category')) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $posts = $query->paginate(9)->withQueryString();

        return view('shop::blog.index', compact('posts', 'categories'));
    }

    /**
     * Display a single blog post.
     */
    public function show(string $slug): View
    {
        $post = BlogPost::with('category')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $recentPosts = BlogPost::with('category')
            ->where('id', '!=', $post->id)
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->latest()
            ->limit(4)
            ->get();

        $social = [
            'facebook' => SiteSetting::getValue('store_facebook'),
            'instagram' => SiteSetting::getValue('store_instagram'),
            'youtube' => SiteSetting::getValue('store_youtube'),
            'tiktok' => SiteSetting::getValue('store_tiktok'),
            'whatsapp' => SiteSetting::getValue('store_whatsapp'),
        ];

        return view('shop::blog.show', compact('post', 'recentPosts', 'social'));
    }
}
