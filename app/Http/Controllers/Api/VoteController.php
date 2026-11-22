<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Vote;
use App\Notifications\IssueUpvoted;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Toggle vote for an issue
     * 
     * @param Request $request
     * @param Issue $issue
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request, Issue $issue)
    {
        $userId = $request->user() ? $request->user()->id : null;
        $ipAddress = $request->ip();

        // Check if already voted
        $existingVote = Vote::where('issue_id', $issue->id)
            ->where(function ($query) use ($userId, $ipAddress) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->first();

        if ($existingVote) {
            // Remove vote
            $existingVote->delete();
            $issue->decrement('votes_count');

            return response()->json([
                'message' => 'Vote removed',
                'voted' => false,
                'votes_count' => $issue->votes_count
            ]);
        } else {
            // Add vote
            Vote::create([
                'issue_id' => $issue->id,
                'user_id' => $userId,
                'ip_address' => $ipAddress
            ]);

            $issue->increment('votes_count');

            // Notify issue owner every 10 votes
            if ($issue->votes_count % 10 === 0 && $issue->user) {
                $issue->user->notify(new IssueUpvoted($issue, $issue->votes_count));
            }

            return response()->json([
                'message' => 'Vote added',
                'voted' => true,
                'votes_count' => $issue->votes_count
            ]);
        }
    }

    /**
     * Check if user has voted
     * 
     * @param Request $request
     * @param Issue $issue
     * @return \Illuminate\Http\JsonResponse
     */
    public function hasVoted(Request $request, Issue $issue)
    {
        $userId = $request->user() ? $request->user()->id : null;
        $ipAddress = $request->ip();

        $hasVoted = Vote::where('issue_id', $issue->id)
            ->where(function ($query) use ($userId, $ipAddress) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->exists();

        return response()->json([
            'voted' => $hasVoted
        ]);
    }

    /**
     * Get voters for an issue
     * 
     * @param Issue $issue
     * @return \Illuminate\Http\JsonResponse
     */
    public function voters(Issue $issue)
    {
        $voters = Vote::where('issue_id', $issue->id)
            ->with('user:id,name,avatar')
            ->latest()
            ->limit(20)
            ->get();

        return response()->json([
            'voters' => $voters,
            'total' => $issue->votes_count
        ]);
    }
}
