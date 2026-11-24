<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Photo;
use App\Services\ImageService;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Get all photos for an issue
     * 
     * @param Issue $issue
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Issue $issue)
    {
        $photos = $issue->photos()
            ->orderBy('order')
            ->get();

        return response()->json($photos);
    }

    /**
     * Upload photos for an issue
     * 
     * @param Request $request
     * @param Issue $issue
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Issue $issue)
    {
        $request->validate([
            'photos' => 'required|array|max:10',
            'photos.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240', // 10MB max
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:500'
        ]);

        $uploadedPhotos = [];
        $photos = $request->file('photos');
        $captions = $request->input('captions', []);

        foreach ($photos as $index => $photo) {
            // Process image
            $processed = $this->imageService->processImage($photo, 'issues/' . $issue->id);

            // Create photo record
            $photoModel = $issue->photos()->create([
                'user_id' => $request->user()->id,
                'path' => $processed['path'],
                'thumbnail_path' => $processed['thumbnail_path'] ?? null,
                'original_name' => $photo->getClientOriginalName(),
                'file_size' => $processed['file_size'],
                'mime_type' => $processed['mime_type'],
                'width' => $processed['width'],
                'height' => $processed['height'],
                'order' => $issue->photos()->count() + $index,
                'caption' => $captions[$index] ?? null
            ]);

            $uploadedPhotos[] = $photoModel;
        }

        // Update photos count
        $issue->increment('photos_count', count($photos));

        return response()->json([
            'message' => count($photos) . ' photo(s) uploaded successfully',
            'photos' => $uploadedPhotos
        ], 201);
    }

    /**
     * Update photo details
     * 
     * @param Request $request
     * @param Photo $photo
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Photo $photo)
    {
        // Check authorization
        if ($photo->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'caption' => 'nullable|string|max:500',
            'order' => 'nullable|integer|min:0'
        ]);

        $photo->update($validated);

        return response()->json([
            'message' => 'Photo updated successfully',
            'photo' => $photo
        ]);
    }

    /**
     * Crop a photo
     * 
     * @param Request $request
     * @param Photo $photo
     * @return \Illuminate\Http\JsonResponse
     */
    public function crop(Request $request, Photo $photo)
    {
        // Check authorization
        if ($photo->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'x' => 'required|integer|min:0',
            'y' => 'required|integer|min:0',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1'
        ]);

        // Crop image
        $cropped = $this->imageService->cropImage(
            $photo->path,
            $validated['x'],
            $validated['y'],
            $validated['width'],
            $validated['height']
        );

        // Update photo record
        $photo->update([
            'path' => $cropped['path'],
            'width' => $cropped['width'],
            'height' => $cropped['height'],
            'file_size' => $cropped['file_size']
        ]);

        // Recreate thumbnail
        $image = \Intervention\Image\Facades\Image::make(\Storage::get($photo->path));
        $thumbnailPath = $this->imageService->createThumbnail(
            $image,
            dirname($photo->path),
            basename($photo->path),
            300,
            300,
            85
        );
        
        $photo->update(['thumbnail_path' => $thumbnailPath]);

        return response()->json([
            'message' => 'Photo cropped successfully',
            'photo' => $photo->fresh()
        ]);
    }

    /**
     * Delete a photo
     * 
     * @param Request $request
     * @param Photo $photo
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Photo $photo)
    {
        // Check authorization
        if ($photo->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $issue = $photo->issue;
        $photo->delete();

        // Decrement photos count
        $issue->decrement('photos_count');

        return response()->json([
            'message' => 'Photo deleted successfully'
        ]);
    }

    /**
     * Reorder photos
     * 
     * @param Request $request
     * @param Issue $issue
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'photos' => 'required|array',
            'photos.*.id' => 'required|exists:photos,id',
            'photos.*.order' => 'required|integer|min:0'
        ]);

        foreach ($validated['photos'] as $photoData) {
            Photo::where('id', $photoData['id'])
                ->where('issue_id', $issue->id)
                ->update(['order' => $photoData['order']]);
        }

        return response()->json([
            'message' => 'Photos reordered successfully'
        ]);
    }
}
