<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\IssueResource;
use App\Models\Issue;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    /**
     * Display a listing of issues.
     */
    public function index(Request $request)
    {
        $query = Issue::with(['category', 'user', 'department'])
            ->withCount(['votes', 'comments']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by department
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $issues = $query->paginate($request->get('per_page', 15));

        return IssueResource::collection($issues);
    }

    /**
     * Store a newly created issue.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $issue = $request->user()->issues()->create($validated);
        $issue->load(['category', 'user', 'department']);

        return new IssueResource($issue);
    }

    /**
     * Display the specified issue.
     */
    public function show(Issue $issue)
    {
        $issue->load([
            'category',
            'user',
            'department',
            'comments.user',
            'photos',
            'votes'
        ])->loadCount(['votes', 'comments']);

        return new IssueResource($issue);
    }

    /**
     * Update the specified issue.
     */
    public function update(Request $request, Issue $issue)
    {
        $this->authorize('update', $issue);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'department_id' => 'nullable|exists:departments,id',
            'location' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $issue->update($validated);
        $issue->load(['category', 'user', 'department']);

        return new IssueResource($issue);
    }

    /**
     * Update issue status.
     */
    public function updateStatus(Request $request, Issue $issue)
    {
        $this->authorize('updateStatus', $issue);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,rejected',
            'resolution_notes' => 'nullable|string',
        ]);

        $issue->update($validated);

        return new IssueResource($issue);
    }

    /**
     * Remove the specified issue.
     */
    public function destroy(Issue $issue)
    {
        $this->authorize('delete', $issue);

        $issue->delete();

        return response()->json([
            'message' => 'Issue deleted successfully.'
        ]);
    }
}
