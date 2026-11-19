<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Issue;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IssueController extends Controller
{
    /**
     * Display the main map view with all issues
     */
    public function index()
    {
        $issues = Issue::with('category')
            ->where('is_verified', true)
            ->latest()
            ->get();

        $categories = Category::where('is_active', true)->get();

        return Inertia::render('Index', [
            'issues' => $issues,
            'categories' => $categories,
        ]);
    }

    /**
     * Display issues board/notice board with pagination and filters
     */
    public function issuesBoard(Request $request)
    {
        $query = Issue::with(['category', 'user'])
            ->withCount(['votes', 'comments'])
            ->where('is_verified', true);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'votes':
                $query->orderBy('votes_count', 'desc');
                break;
            case 'comments':
                $query->orderBy('comments_count', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest();
        }

        $issues = $query->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)->get();

        return Inertia::render('Issues/Index', [
            'issues' => $issues,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'status', 'sort'])
        ]);
    }

    /**
     * Show the form for creating a new issue
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();

        return Inertia::render('Report', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created issue
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'nullable|image|max:10240', // 10MB max
            'reporter_name' => 'nullable|string|max:255',
            'reporter_email' => 'nullable|email|max:255',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('issues', 'public');
            $validated['photo_path'] = $path;
        }

        // Create the issue
        Issue::create($validated);

        return redirect()->route('home')->with('success', 'Issue reported successfully!');
    }

    /**
     * Display a single verified issue
     */
    public function show(Issue $issue)
    {
        if (!$issue->is_verified) {
            abort(404);
        }

        $issue->load(['category', 'user'])
            ->loadCount(['votes', 'comments']);

        return Inertia::render('Issues/Show', [
            'issue' => $issue,
        ]);
    }

    /**
     * API endpoint to get all verified issues
     */
    public function apiIndex()
    {
        $issues = Issue::with('category')
            ->where('is_verified', true)
            ->latest()
            ->get();

        return response()->json($issues);
    }

    /**
     * API endpoint to get a single issue
     */
    public function apiShow(Issue $issue)
    {
        if (!$issue->is_verified) {
            return response()->json(['message' => 'Issue not found'], 404);
        }

        $issue->load('category');
        return response()->json($issue);
    }

    /**
     * API endpoint to get categories
     */
    public function apiCategories()
    {
        $categories = Category::where('is_active', true)->get();
        return response()->json($categories);
    }
}
