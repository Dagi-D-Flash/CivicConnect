<?php

namespace App\Services;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GuestVoteService
{
    /**
     * Track guest vote in session and cache.
     */
    public function trackGuestVote(Request $request, Issue $issue): void
    {
        $guestId = $this->getGuestId($request);
        $cacheKey = "guest_vote_{$guestId}_{$issue->id}";
        
        // Store in cache for 30 days
        Cache::put($cacheKey, true, now()->addDays(30));
        
        // Also track in session
        $sessionKey = "voted_issues";
        $votedIssues = $request->session()->get($sessionKey, []);
        
        if (!in_array($issue->id, $votedIssues)) {
            $votedIssues[] = $issue->id;
            $request->session()->put($sessionKey, $votedIssues);
        }
    }

    /**
     * Check if guest has voted on issue.
     */
    public function hasGuestVoted(Request $request, Issue $issue): bool
    {
        $guestId = $this->getGuestId($request);
        $cacheKey = "guest_vote_{$guestId}_{$issue->id}";
        
        // Check cache first
        if (Cache::has($cacheKey)) {
            return true;
        }
        
        // Check session
        $votedIssues = $request->session()->get('voted_issues', []);
        return in_array($issue->id, $votedIssues);
    }

    /**
     * Remove guest vote tracking.
     */
    public function removeGuestVote(Request $request, Issue $issue): void
    {
        $guestId = $this->getGuestId($request);
        $cacheKey = "guest_vote_{$guestId}_{$issue->id}";
        
        // Remove from cache
        Cache::forget($cacheKey);
        
        // Remove from session
        $sessionKey = "voted_issues";
        $votedIssues = $request->session()->get($sessionKey, []);
        
        if (($key = array_search($issue->id, $votedIssues)) !== false) {
            unset($votedIssues[$key]);
            $request->session()->put($sessionKey, array_values($votedIssues));
        }
    }

    /**
     * Get unique guest identifier.
     */
    protected function getGuestId(Request $request): string
    {
        // Use session ID as guest identifier
        if (!$request->session()->has('guest_id')) {
            $request->session()->put('guest_id', uniqid('guest_', true));
        }
        
        return $request->session()->get('guest_id');
    }

    /**
     * Get all issues voted by guest.
     */
    public function getGuestVotedIssues(Request $request): array
    {
        return $request->session()->get('voted_issues', []);
    }
}
