@extends('layouts.master')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <article class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($post->featured_image)
        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-96 object-cover">
        @endif
        <div class="p-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $post->title }}</h1>

            <div class="flex items-center text-sm text-gray-500 mb-6">
                <span>Published on: {{ $post->published_at->format('F j, Y') }}</span>
                @if($post->topic->isNotEmpty())
                <span class="mx-2">•</span>
                <span class="bg-gray-100 px-2 py-1 rounded">{{ $post->topic->first()->name }}</span>
                @endif
            </div>

            <div class="prose max-w-none text-gray-700 mb-8">
                {!! $post->body !!}
            </div>

            @if($post->tags->count() > 0)
            <div class="border-t pt-6">
                <h4 class="text-sm font-semibold text-gray-900 mb-2">Tags:</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                        {{ $tag->name }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </article>

    <!-- Related Posts -->
    @if($relatedPosts->count() > 0)
    <div class="mt-12">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">Related Posts</h3>
        <div class="grid gap-6 md:grid-cols-3">
            @foreach($relatedPosts as $relatedPost)
            <div class="bg-white rounded-lg shadow-md p-4">
                <h4 class="text-lg font-semibold text-gray-800 mb-2">
                    <a href="{{ route('blog.show', $relatedPost->slug) }}" class="hover:text-blue-600">
                        {{ $relatedPost->title }}
                    </a>
                </h4>
                <p class="text-gray-600 text-sm">{{ $relatedPost->published_at->format('M j, Y') }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Back to Blog -->
    <div class="mt-8 text-center">
        <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 font-medium">
            ← Back to Home
        </a>
    </div>
</div>
@endsection
