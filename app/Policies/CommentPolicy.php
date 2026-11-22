<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * Determine if the user can view any comments.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the comment.
     */
    public function view(?User $user, Comment $comment): bool
    {
        return true;
    }

    /**
     * Determine if the user can create comments.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        // Users can only edit their own comments
        return $user->id === $comment->user_id;
    }

    /**
     * Determine if the user can delete the comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // Users can delete their own comments, admins can delete any
        return $user->id === $comment->user_id || $user->is_admin;
    }

    /**
     * Determine if the user can restore the comment.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine if the user can permanently delete the comment.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return $user->is_admin;
    }
}
