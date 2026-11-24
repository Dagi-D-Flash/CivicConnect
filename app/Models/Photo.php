<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'user_id',
        'path',
        'thumbnail_path',
        'original_name',
        'file_size',
        'mime_type',
        'width',
        'height',
        'order',
        'caption'
    ];

    protected $appends = ['url', 'thumbnail_url'];

    /**
     * Get the issue that owns the photo
     */
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    /**
     * Get the user that uploaded the photo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full URL of the photo
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    /**
     * Get the thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path 
            ? Storage::url($this->thumbnail_path)
            : $this->url;
    }

    /**
     * Get human-readable file size
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < 3) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Delete photo files from storage
     */
    public function deleteFiles()
    {
        Storage::delete($this->path);
        
        if ($this->thumbnail_path) {
            Storage::delete($this->thumbnail_path);
        }
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($photo) {
            $photo->deleteFiles();
        });
    }
}
