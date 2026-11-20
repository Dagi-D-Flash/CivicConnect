<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Road Issues',
                'slug' => 'road-issues',
                'icon' => 'truck',
                'color' => '#EF4444',
                'description' => 'Potholes, damaged roads, road cracks',
            ],
            [
                'name' => 'Waste Management',
                'slug' => 'waste-management',
                'icon' => 'trash',
                'color' => '#10B981',
                'description' => 'Garbage collection, illegal dumping, overflowing bins',
            ],
            [
                'name' => 'Street Lighting',
                'slug' => 'street-lighting',
                'icon' => 'light-bulb',
                'color' => '#F59E0B',
                'description' => 'Broken lights, dark streets, faulty poles',
            ],
            [
                'name' => 'Water & Drainage',
                'slug' => 'water-drainage',
                'icon' => 'wrench-screwdriver',
                'color' => '#3B82F6',
                'description' => 'Flooding, blocked drains, water leaks',
            ],
            [
                'name' => 'Electricity',
                'slug' => 'electricity',
                'icon' => 'bolt',
                'color' => '#8B5CF6',
                'description' => 'Power outages, damaged cables, faulty transformers',
            ],
            [
                'name' => 'Public Safety',
                'slug' => 'public-safety',
                'icon' => 'shield-exclamation',
                'color' => '#DC2626',
                'description' => 'Dangerous structures, security concerns',
            ],
            [
                'name' => 'Parks & Recreation',
                'slug' => 'parks-recreation',
                'icon' => 'building-library',
                'color' => '#059669',
                'description' => 'Park maintenance, playground issues, green spaces',
            ],
            [
                'name' => 'Other',
                'slug' => 'other',
                'icon' => 'ellipsis-horizontal-circle',
                'color' => '#6B7280',
                'description' => 'Other community issues',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
