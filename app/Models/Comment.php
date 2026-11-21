<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'user_id',
        'content',
        'parent_id'
    ];

    protected $with = ['user'];

    protected $appends = ['replies_count'];

    /**
     * Get the issue that owns the comment
     */
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    /**
     * Get the user that owns the comment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the replies to this comment
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }

    /**
     * Get replies count
     */
    public function getRepliesCountAttribute()
    {
        return $this->replies()->count();
    }

    /**
     * Scope a query to only include parent comments (not replies)
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
