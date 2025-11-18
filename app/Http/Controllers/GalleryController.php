<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        // Look in the unified location where both systems store images
        $imagePath = 'canvas/images';
        $images = collect();

        try {
            $disk = Storage::disk('public');

            if ($disk->exists($imagePath)) {
                $files = $disk->allFiles($imagePath);

                $images = collect($files)->filter(function ($file) {
                    // Ensure $file is a string and matches image pattern
                    return is_string($file) &&
                           !empty($file) &&
                           preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
                })->map(function ($file) {
                    // For public disk, files are accessible via storage symlink
                    // Ensure $file is a valid string before processing
                    if (!is_string($file) || empty($file)) {
                        return null;
                    }

                    return [
                        'path' => $file,
                        'url' => asset('storage/' . $file),
                        'name' => basename($file)
                    ];
                })->filter(); // Remove any null entries
            }
        } catch (\Exception $e) {
            // Log error and continue with empty images collection
            $images = collect();
        }

        // Paginate the results (12 images per page)
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $currentItems = $images->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedImages = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $images->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return view('gallery.index', compact('paginatedImages'));
    }
}
