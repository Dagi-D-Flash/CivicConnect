<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vote;

class VotePolicy
{
    /**
     * Determine if the user can create votes.
     */
    public function create(?User $user): bool
    {
        // Both authenticated and guest users can vote
        return true;
    }

    /**
     * Determine if the user can delete the vote.
     */
    public function delete(User $user, Vote $vote): bool
    {
        // Users can only remove their own votes
        return $user->id === $vote->user_id;
    }
}
