<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category_id',
        'latitude',
        'longitude',
        'address',
        'photo_path',
        'status',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'is_verified',
        'verified_at',
        'resolved_at',
        'admin_notes',
        'votes_count',
        'comments_count',
        'assigned_to',
        'department_id',
        'due_date',
        'priority',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'resolved_at' => 'datetime',
        'due_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function votes()
    {
        return $this->hasMany(IssueVote::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scopes
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePopular($query, $minVotes = 5)
    {
        return $query->where('votes_count', '>=', $minVotes);
    }

    public function scopeNearby($query, $lat, $lng, $radiusKm = 5)
    {
        // Simple distance calculation (not accurate for large distances)
        $query->selectRaw("*, 
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
            sin(radians(latitude)))) AS distance", [$lat, $lng, $lat])
            ->having('distance', '<', $radiusKm)
            ->orderBy('distance');
    }

    /**
     * Check if user has voted
     */
    public function hasVoted($userId = null, $ipAddress = null)
    {
        $query = $this->votes();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        if ($ipAddress) {
            $query->orWhere('ip_address', $ipAddress);
        }
        
        return $query->exists();
    }
}
