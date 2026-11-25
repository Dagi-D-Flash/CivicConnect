<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

class FilterService
{
    public function applyFilters(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            if (empty($value)) {
                continue;
            }

            match($key) {
                'status' => $query->where('status', $value),
                'category_id' => $query->where('category_id', $value),
                'department_id' => $query->where('department_id', $value),
                'priority' => $query->where('priority', $value),
                'user_id' => $query->where('user_id', $value),
                'has_photos' => $query->has('photos'),
                'min_votes' => $query->withCount('votes')->having('votes_count', '>=', $value),
                'max_votes' => $query->withCount('votes')->having('votes_count', '<=', $value),
                default => null
            };
        }

        return $query;
    }

    public function getAvailableFilters(): array
    {
        return [
            'status' => ['pending', 'in_progress', 'resolved', 'rejected'],
            'priority' => ['low', 'medium', 'high', 'urgent'],
            'sort_by' => ['created_at', 'updated_at', 'votes_count', 'comments_count'],
            'sort_order' => ['asc', 'desc'],
        ];
    }
}
