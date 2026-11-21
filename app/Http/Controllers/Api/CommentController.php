<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Issue;
use App\Notifications\CommentAdded;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Get all comments for an issue
     * 
     * @param Issue $issue
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Issue $issue)
    {
        $comments = Comment::where('issue_id', $issue->id)
            ->parents()
            ->with(['replies.user'])
            ->latest()
            ->paginate(10);

        return response()->json($comments);
    }

    /**
     * Store a new comment
     * 
     * @param Request $request
     * @param Issue $issue
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = $issue->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null
        ]);

        // Increment comments count
        $issue->increment('comments_count');

        // Notify issue owner
        if ($issue->user_id !== $request->user()->id) {
            $issue->user->notify(new CommentAdded($comment, $issue));
        }

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user')
        ], 201);
    }

    /**
     * Update a comment
     * 
     * @param Request $request
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Comment $comment)
    {
        // Check if user owns the comment
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment->update($validated);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment
        ]);
    }

    /**
     * Delete a comment
     * 
     * @param Request $request
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Comment $comment)
    {
        // Check if user owns the comment
        if ($comment->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $issue = $comment->issue;
        $comment->delete();

        // Decrement comments count
        $issue->decrement('comments_count');

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
