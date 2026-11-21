<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserStatsService
{
    /**
     * Get user statistics.
     */
    public function getUserStats(User $user): array
    {
        return Cache::remember("user_stats_{$user->id}", 3600, function () use ($user) {
            return [
                'issues_count' => $user->issues()->count(),
                'resolved_issues_count' => $user->issues()->where('status', 'resolved')->count(),
                'comments_count' => $user->comments()->count(),
                'votes_count' => $user->votes()->count(),
                'points' => $this->calculateUserPoints($user),
                'level' => $this->calculateUserLevel($user),
                'badges' => $this->getUserBadges($user),
            ];
        });
    }

    /**
     * Calculate user points based on activity.
     */
    protected function calculateUserPoints(User $user): int
    {
        $points = 0;
        
        // Points for creating issues
        $points += $user->issues()->count() * 10;
        
        // Points for resolved issues
        $points += $user->issues()->where('status', 'resolved')->count() * 50;
        
        // Points for comments
        $points += $user->comments()->count() * 5;
        
        // Points for votes received on their issues
        $points += $user->issues()->withCount('votes')->get()->sum('votes_count') * 2;
        
        return $points;
    }

    /**
     * Calculate user level based on points.
     */
    protected function calculateUserLevel(User $user): int
    {
        $points = $this->calculateUserPoints($user);
        
        return (int) floor($points / 100) + 1;
    }

    /**
     * Get user badges.
     */
    protected function getUserBadges(User $user): array
    {
        $badges = [];
        
        // First Issue Badge
        if ($user->issues()->count() >= 1) {
            $badges[] = ['name' => 'First Reporter', 'icon' => '🎯'];
        }
        
        // Active Participant Badge
        if ($user->issues()->count() >= 10) {
            $badges[] = ['name' => 'Active Participant', 'icon' => '⭐'];
        }
        
        // Problem Solver Badge
        if ($user->issues()->where('status', 'resolved')->count() >= 5) {
            $badges[] = ['name' => 'Problem Solver', 'icon' => '🏆'];
        }
        
        // Community Builder Badge
        if ($user->comments()->count() >= 50) {
            $badges[] = ['name' => 'Community Builder', 'icon' => '🤝'];
        }
        
        return $badges;
    }

    /**
     * Clear user stats cache.
     */
    public function clearUserStatsCache(User $user): void
    {
        Cache::forget("user_stats_{$user->id}");
    }
}
