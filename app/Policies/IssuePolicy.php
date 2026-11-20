<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;

class IssuePolicy
{
    /**
     * Determine if the user can view any issues.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the issue.
     */
    public function view(?User $user, Issue $issue): bool
    {
        return true;
    }

    /**
     * Determine if the user can create issues.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the issue.
     */
    public function update(User $user, Issue $issue): bool
    {
        // Users can only edit their own issues
        // Admins can edit any issue
        return $user->id === $issue->user_id || $user->is_admin;
    }

    /**
     * Determine if the user can update issue status.
     */
    public function updateStatus(User $user, Issue $issue): bool
    {
        // Only admins or department officials can update status
        return $user->is_admin || $user->is_department_official;
    }

    /**
     * Determine if the user can delete the issue.
     */
    public function delete(User $user, Issue $issue): bool
    {
        // Users can delete their own issues, admins can delete any
        return $user->id === $issue->user_id || $user->is_admin;
    }

    /**
     * Determine if the user can restore the issue.
     */
    public function restore(User $user, Issue $issue): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if the user can permanently delete the issue.
     */
    public function forceDelete(User $user, Issue $issue): bool
    {
        return $user->is_admin;
    }
}
