<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Get comments for an issue
     */
    public function index(Issue $issue)
    {
        $query = $issue->comments()
            ->with(['user:id,name,alias,is_anonymous,avatar'])
            ->latest();

        // Non-admins can only see public comments
        if (!Auth::check() || !Auth::user()->is_admin) {
            $query->where('is_internal', false);
        }

        $comments = $query->paginate(20);

        // Hide user details if anonymous
        $comments->getCollection()->transform(function ($comment) {
            if ($comment->user && $comment->user->is_anonymous) {
                $comment->user->name = $comment->user->alias ?? 'Anonymous';
                $comment->user->makeHidden(['email']);
            }
            return $comment;
        });

        return response()->json($comments);
    }

    /**
     * Store a new comment
     */
    public function store(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_internal' => 'boolean',
        ]);

        $comment = Comment::create([
            'issue_id' => $issue->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'is_admin' => Auth::check() && Auth::user()->is_admin,
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        // Increment comment count
        $issue->increment('comments_count');

        // Award reputation points
        if (Auth::check()) {
            Auth::user()->increment('reputation_points', 2);
        }

        // TODO: Send notification to issue owner

        $comment->load('user:id,name,alias,is_anonymous,avatar');

        return response()->json([
            'comment' => $comment,
            'message' => 'Comment added successfully'
        ], 201);
    }

    /**
     * Update a comment
     */
    public function update(Request $request, Comment $comment)
    {
        // Check authorization
        if (!Auth::check() || (Auth::id() !== $comment->user_id && !Auth::user()->is_admin)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $validated['content'],
        ]);

        return response()->json([
            'comment' => $comment,
            'message' => 'Comment updated successfully'
        ]);
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment)
    {
        // Check authorization
        if (!Auth::check() || (Auth::id() !== $comment->user_id && !Auth::user()->is_admin)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $issueId = $comment->issue_id;
        $comment->delete();

        // Decrement comment count
        Issue::find($issueId)->decrement('comments_count');

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
