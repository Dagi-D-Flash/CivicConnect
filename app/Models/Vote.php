<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'user_id',
        'ip_address'
    ];

    /**
     * Get the issue that owns the vote
     */
    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    /**
     * Get the user that owns the vote
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
