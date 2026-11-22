<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\IssueVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    /**
     * Toggle vote on an issue
     */
    public function toggle(Request $request, Issue $issue)
    {
        $userId = Auth::id();
        $ipAddress = $request->ip();

        // Check if already voted
        $existingVote = IssueVote::where('issue_id', $issue->id)
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
            
            // Remove reputation points if user exists
            if ($issue->user_id) {
                $issue->user->decrement('reputation_points', 5);
            }

            return response()->json([
                'voted' => false,
                'votes_count' => $issue->fresh()->votes_count,
                'message' => 'Vote removed'
            ]);
        } else {
            // Add vote
            IssueVote::create([
                'issue_id' => $issue->id,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
            ]);
            
            $issue->increment('votes_count');
            
            // Award reputation points if user exists
            if ($issue->user_id) {
                $issue->user->increment('reputation_points', 5);
            }

            return response()->json([
                'voted' => true,
                'votes_count' => $issue->fresh()->votes_count,
                'message' => 'Vote added'
            ]);
        }
    }

    /**
     * Get voters for an issue
     */
    public function voters(Issue $issue)
    {
        $votes = $issue->votes()
            ->with('user:id,name,alias,is_anonymous,avatar')
            ->latest()
            ->paginate(20);

        // Hide user details if anonymous
        $votes->getCollection()->transform(function ($vote) {
            if ($vote->user && $vote->user->is_anonymous) {
                $vote->user->name = $vote->user->alias ?? 'Anonymous';
                $vote->user->makeHidden(['email']);
            }
            return $vote;
        });

        return response()->json($votes);
    }

    /**
     * Check if current user/IP has voted
     */
    public function hasVoted(Request $request, Issue $issue)
    {
        $userId = Auth::id();
        $ipAddress = $request->ip();

        $hasVoted = $issue->hasVoted($userId, $ipAddress);

        return response()->json([
            'has_voted' => $hasVoted,
            'votes_count' => $issue->votes_count
        ]);
    }
}
