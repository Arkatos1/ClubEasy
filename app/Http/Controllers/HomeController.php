<?php

namespace App\Http\Controllers;

use Canvas\Models\Post;
use Canvas\Models\Topic;
use Canvas\Models\Tag;
use Canvas\Models\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // All posts with pagination - eager load relationships
        $posts = Post::published()
                    ->with(['topic', 'user', 'tags'])
                    ->orderBy('published_at', 'desc')
                    ->paginate(4);

        // Popular posts - eager load relationships
        $popularPosts = Post::published()
                        ->with(['topic', 'user', 'tags'])
                        ->withCount('views')
                        ->orderBy('views_count', 'desc')
                        ->take(10)
                        ->get();

        // If we need to handle the case where withCount doesn't work, we can use a raw query
        if ($popularPosts->isEmpty()) {
            $popularPosts = Post::published()
                               ->with(['topic', 'user', 'tags'])
                               ->get()
                               ->sortByDesc(function($post) {
                                   return $post->views()->count();
                               })
                               ->take(5);
        }

        // Topics for filtering
        $topics = Topic::withCount('posts')->having('posts_count', '>', 0)->get();

        // Latest topics
        $latestTopics = Topic::latest()->take(5)->get();

        return view('pages.home', compact(
            'posts',
            'popularPosts',
            'topics',
            'latestTopics'
        ));
    }

    public function showPost($slug)
    {
        $post = Post::with(['topic', 'tags', 'user'])
                   ->where('slug', $slug)
                   ->published()
                   ->firstOrFail();

        // Create a view record
        View::create([
            'post_id' => $post->id,
            'ip' => request()->ip(),
            'agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
        ]);

        $relatedPosts = Post::published()
                           ->with(['topic', 'user'])
                           ->where('id', '!=', $post->id)
                           ->where(function($query) use ($post) {
                               if ($post->topic->isNotEmpty()) {
                                   $query->whereHas('topic', function($q) use ($post) {
                                       $q->where('id', $post->topic->first()->id);
                                   });
                               }
                           })
                           ->inRandomOrder()
                           ->limit(3)
                           ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    public function topic($slug)
    {
        $topic = Topic::where('slug', $slug)->firstOrFail();
        $posts = Post::published()
                    ->whereHas('topic', function($query) use ($topic) {
                        $query->where('id', $topic->id);
                    })
                    ->orderBy('published_at', 'desc')
                    ->paginate(9);

        return view('blog.topic', compact('topic', 'posts'));
    }
}
