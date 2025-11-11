@extends('layouts.master')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Topic: {{ $topic->name }}</h1>
        <p class="text-gray-600">{{ $posts->total() }} posts in this category</p>
    </div>

    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
        @foreach($posts as $post)
        <article class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
            @if($post->featured_image)
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
            @endif
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-3">
                    <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-600">
                        {{ $post->title }}
                    </a>
                </h2>
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                    {!! Str::limit(strip_tags($post->body), 120) !!}
                </p>
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>{{ $post->published_at->format('M j, Y') }}</span>
                    <span>{{ $post->view_count }} views</span>
                </div>
            </div>
        </article>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
</div>
@endsection
