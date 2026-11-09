<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'alias',
        'is_anonymous',
        'email',
        'phone',
        'password',
        'bio',
        'avatar',
        'reputation_points',
        'notification_preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_anonymous' => 'boolean',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * Get the display name (alias if anonymous, otherwise name)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->is_anonymous && $this->alias ? $this->alias : $this->name;
    }

    /**
     * Relationships
     */
    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function votes()
    {
        return $this->hasMany(IssueVote::class);
    }

    public function assignedIssues()
    {
        return $this->hasMany(Issue::class, 'assigned_to');
    }

    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'manager_id');
    }
}
