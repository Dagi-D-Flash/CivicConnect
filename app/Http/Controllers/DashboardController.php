<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * User dashboard overview
     */
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'issues_reported' => $user->issues()->count(),
            'issues_resolved' => $user->issues()->where('status', 'resolved')->count(),
            'total_votes' => $user->votes()->count(),
            'total_comments' => $user->comments()->count(),
            'reputation_points' => $user->reputation_points,
        ];

        $recentIssues = $user->issues()
            ->with('category')
            ->latest()
            ->take(5)
            ->get();

        $recentComments = $user->comments()
            ->with('issue:id,title')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'recentIssues' => $recentIssues,
            'recentComments' => $recentComments,
        ]);
    }

    /**
     * User's reported issues
     */
    public function myIssues()
    {
        $issues = Auth::user()->issues()
            ->with(['category'])
            ->withCount(['votes', 'comments'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Dashboard/MyIssues', [
            'issues' => $issues,
        ]);
    }

    /**
     * Issues user has voted on
     */
    public function myVotes()
    {
        $votes = Auth::user()->votes()
            ->with(['issue.category'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Dashboard/MyVotes', [
            'votes' => $votes,
        ]);
    }

    /**
     * User's comments
     */
    public function myComments()
    {
        $comments = Auth::user()->comments()
            ->with(['issue:id,title,status'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Dashboard/MyComments', [
            'comments' => $comments,
        ]);
    }

    /**
     * User profile
     */
    public function profile()
    {
        $user = Auth::user();

        return Inertia::render('Dashboard/Profile', [
            'user' => $user,
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'is_anonymous' => 'boolean',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048',
            'notification_preferences' => 'nullable|array',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully');
    }
}
