@extends('layouts.master')

@section('title', __('Photo Gallery'))

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Photo Gallery') }}</h1>
    </div>

    @if($paginatedImages->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($paginatedImages as $image)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <img
                        src="{{ $image['url'] }}"
                        alt="Gallery image"
                        class="w-full h-64 object-cover cursor-pointer"
                        loading="lazy"
                        onclick="openModal('{{ $image['url'] }}')"
                    >
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $paginatedImages->links() }}
        </div>

        <!-- Modal for larger image view -->
        <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50 p-4">
            <div class="max-w-4xl max-h-full">
                <div class="relative">
                    <button
                        onclick="closeModal()"
                        class="absolute -top-12 -right-4 text-white text-3xl hover:text-gray-300 bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center"
                    >
                        &times;
                    </button>
                    <img id="modalImage" src="" alt="Full size" class="max-w-full max-h-screen object-contain">
                </div>
            </div>
        </div>

    @else
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">
                📷
            </div>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">{{ __('No images yet') }}</h3>
        </div>
    @endif
</div>

<script>
function openModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside the image
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target.id === 'imageModal') {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>

<style>
/* Custom pagination styles */
.pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 2rem 0;
}

.pagination li {
    margin: 0 0.25rem;
}

.pagination li a,
.pagination li span {
    display: block;
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    text-decoration: none;
    color: #374151;
    transition: all 0.2s ease;
}

.pagination li a:hover {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.pagination li.active span {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.pagination li.disabled span {
    color: #9ca3af;
    background-color: #f3f4f6;
    border-color: #d1d5db;
}
</style>
@endsection
