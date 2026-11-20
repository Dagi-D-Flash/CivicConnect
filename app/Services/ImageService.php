<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageService
{
    /**
     * Process and store an uploaded image
     * 
     * @param UploadedFile $file
     * @param string $directory
     * @param array $options
     * @return array
     */
    public function processImage(UploadedFile $file, string $directory = 'photos', array $options = []): array
    {
        // Default options
        $maxWidth = $options['max_width'] ?? 1920;
        $maxHeight = $options['max_height'] ?? 1920;
        $quality = $options['quality'] ?? 85;
        $createThumbnail = $options['create_thumbnail'] ?? true;
        $thumbnailWidth = $options['thumbnail_width'] ?? 300;
        $thumbnailHeight = $options['thumbnail_height'] ?? 300;

        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $directory . '/' . $filename;

        // Load image
        $image = Image::make($file);
        
        // Get original dimensions
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        // Resize if needed
        if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
            $image->resize($maxWidth, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Fix orientation from EXIF
        $image->orientate();

        // Save to storage
        Storage::put($path, (string) $image->encode(null, $quality));

        $result = [
            'path' => $path,
            'width' => $image->width(),
            'height' => $image->height(),
            'file_size' => Storage::size($path),
            'mime_type' => Storage::mimeType($path),
        ];

        // Create thumbnail
        if ($createThumbnail) {
            $thumbnailPath = $this->createThumbnail($image, $directory, $filename, $thumbnailWidth, $thumbnailHeight, $quality);
            $result['thumbnail_path'] = $thumbnailPath;
        }

        return $result;
    }

    /**
     * Create thumbnail from image
     * 
     * @param \Intervention\Image\Image $image
     * @param string $directory
     * @param string $filename
     * @param int $width
     * @param int $height
     * @param int $quality
     * @return string
     */
    protected function createThumbnail($image, string $directory, string $filename, int $width, int $height, int $quality): string
    {
        $thumbnailFilename = 'thumb_' . $filename;
        $thumbnailPath = $directory . '/thumbnails/' . $thumbnailFilename;

        // Clone image for thumbnail
        $thumbnail = clone $image;

        // Resize to fit within thumbnail dimensions
        $thumbnail->fit($width, $height);

        // Save thumbnail
        Storage::put($thumbnailPath, (string) $thumbnail->encode(null, $quality));

        return $thumbnailPath;
    }

    /**
     * Crop image
     * 
     * @param string $path
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return array
     */
    public function cropImage(string $path, int $x, int $y, int $width, int $height): array
    {
        $image = Image::make(Storage::get($path));

        // Crop image
        $image->crop($width, $height, $x, $y);

        // Generate new filename
        $filename = 'cropped_' . time() . '_' . uniqid() . '.jpg';
        $directory = dirname($path);
        $newPath = $directory . '/' . $filename;

        // Save cropped image
        Storage::put($newPath, (string) $image->encode('jpg', 85));

        // Delete old image
        Storage::delete($path);

        return [
            'path' => $newPath,
            'width' => $image->width(),
            'height' => $image->height(),
            'file_size' => Storage::size($newPath),
        ];
    }

    /**
     * Delete image and its thumbnail
     * 
     * @param string $path
     * @param string|null $thumbnailPath
     * @return bool
     */
    public function deleteImage(string $path, ?string $thumbnailPath = null): bool
    {
        $deleted = Storage::delete($path);

        if ($thumbnailPath) {
            Storage::delete($thumbnailPath);
        }

        return $deleted;
    }

    /**
     * Optimize image
     * 
     * @param string $path
     * @param int $quality
     * @return bool
     */
    public function optimizeImage(string $path, int $quality = 85): bool
    {
        $image = Image::make(Storage::get($path));
        Storage::put($path, (string) $image->encode(null, $quality));
        
        return true;
    }
}
