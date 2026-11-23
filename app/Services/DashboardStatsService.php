<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * Get overall dashboard statistics.
     */
    public function getOverallStats(): array
    {
        return Cache::remember('dashboard_overall_stats', 600, function () {
            return [
                'total_issues' => Issue::count(),
                'pending_issues' => Issue::where('status', 'pending')->count(),
                'in_progress_issues' => Issue::where('status', 'in_progress')->count(),
                'resolved_issues' => Issue::where('status', 'resolved')->count(),
                'total_users' => User::count(),
                'total_comments' => Comment::count(),
                'total_votes' => Vote::count(),
                'resolution_rate' => $this->calculateResolutionRate(),
            ];
        });
    }

    /**
     * Get issues by category statistics.
     */
    public function getIssuesByCategory(): array
    {
        return Cache::remember('dashboard_issues_by_category', 600, function () {
            return Issue::select('category_id', DB::raw('count(*) as count'))
                ->with('category:id,name')
                ->groupBy('category_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'category' => $item->category->name,
                        'count' => $item->count,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get issues by status over time.
     */
    public function getIssuesTrend(int $days = 30): array
    {
        return Cache::remember("dashboard_issues_trend_{$days}", 600, function () use ($days) {
            $startDate = now()->subDays($days);
            
            return Issue::where('created_at', '>=', $startDate)
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('count(*) as count'),
                    'status'
                )
                ->groupBy('date', 'status')
                ->orderBy('date')
                ->get()
                ->groupBy('date')
                ->map(function ($items, $date) {
                    return [
                        'date' => $date,
                        'pending' => $items->where('status', 'pending')->sum('count'),
                        'in_progress' => $items->where('status', 'in_progress')->sum('count'),
                        'resolved' => $items->where('status', 'resolved')->sum('count'),
                        'rejected' => $items->where('status', 'rejected')->sum('count'),
                    ];
                })
                ->values()
                ->toArray();
        });
    }

    /**
     * Get most active users.
     */
    public function getMostActiveUsers(int $limit = 10): array
    {
        return Cache::remember("dashboard_active_users_{$limit}", 600, function () use ($limit) {
            return User::withCount(['issues', 'comments', 'votes'])
                ->orderByDesc('issues_count')
                ->limit($limit)
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => $user->avatar,
                        'issues_count' => $user->issues_count,
                        'comments_count' => $user->comments_count,
                        'votes_count' => $user->votes_count,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Calculate resolution rate.
     */
    protected function calculateResolutionRate(): float
    {
        $total = Issue::count();
        
        if ($total === 0) {
            return 0;
        }
        
        $resolved = Issue::where('status', 'resolved')->count();
        
        return round(($resolved / $total) * 100, 2);
    }

    /**
     * Clear dashboard statistics cache.
     */
    public function clearCache(): void
    {
        Cache::forget('dashboard_overall_stats');
        Cache::forget('dashboard_issues_by_category');
        
        // Clear trend caches
        foreach ([7, 30, 90] as $days) {
            Cache::forget("dashboard_issues_trend_{$days}");
        }
        
        // Clear active users cache
        foreach ([5, 10, 20] as $limit) {
            Cache::forget("dashboard_active_users_{$limit}");
        }
    }
}
