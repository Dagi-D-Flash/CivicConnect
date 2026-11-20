<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Database\Seeder;

class IssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $users = User::all();

        $issues = [
            [
                'user_id' => $users->random()->id ?? null,
                'title' => 'Large pothole on Avenue Kennedy',
                'description' => 'There is a dangerous pothole near the roundabout that has been growing larger. It poses a risk to vehicles and motorcycles.',
                'category_id' => $categories->where('slug', 'road-issues')->first()->id,
                'latitude' => 3.8480,
                'longitude' => 11.5021,
                'address' => 'Avenue Kennedy, Yaoundé',
                'status' => 'pending',
                'is_verified' => true,
                'verified_at' => now(),
                'votes_count' => 15,
                'comments_count' => 3,
                'priority' => 8,
            ],
            [
                'user_id' => $users->random()->id ?? null,
                'title' => 'Overflowing garbage bins at Marché Central',
                'description' => 'The garbage bins near the central market have been overflowing for days. This is creating unsanitary conditions.',
                'category_id' => $categories->where('slug', 'waste-management')->first()->id,
                'latitude' => 3.8667,
                'longitude' => 11.5167,
                'address' => 'Marché Central, Yaoundé',
                'status' => 'in_progress',
                'is_verified' => true,
                'verified_at' => now()->subDays(2),
                'votes_count' => 23,
                'comments_count' => 5,
                'priority' => 9,
            ],
            [
                'user_id' => $users->random()->id ?? null,
                'title' => 'Broken street light on Rue de la Réunification',
                'description' => 'The street light has been out for over a week, making the area very dark and unsafe at night.',
                'category_id' => $categories->where('slug', 'street-lighting')->first()->id,
                'latitude' => 3.8578,
                'longitude' => 11.5189,
                'address' => 'Rue de la Réunification, Yaoundé',
                'status' => 'pending',
                'is_verified' => true,
                'verified_at' => now()->subDays(1),
                'votes_count' => 12,
                'comments_count' => 2,
                'priority' => 7,
            ],
            [
                'user_id' => $users->random()->id ?? null,
                'title' => 'Blocked drainage causing flooding',
                'description' => 'The drainage system is completely blocked, causing water to accumulate during rain. This affects several houses.',
                'category_id' => $categories->where('slug', 'water-drainage')->first()->id,
                'latitude' => 3.8523,
                'longitude' => 11.5098,
                'address' => 'Quartier Bastos, Yaoundé',
                'status' => 'resolved',
                'is_verified' => true,
                'verified_at' => now()->subDays(10),
                'resolved_at' => now()->subDays(2),
                'votes_count' => 31,
                'comments_count' => 8,
                'priority' => 10,
            ],
            [
                'user_id' => $users->random()->id ?? null,
                'title' => 'Damaged playground equipment',
                'description' => 'The swings at the neighborhood park are broken and potentially dangerous for children.',
                'category_id' => $categories->where('slug', 'parks-recreation')->first()->id,
                'latitude' => 3.8612,
                'longitude' => 11.5234,
                'address' => 'Parc Municipal, Yaoundé',
                'status' => 'pending',
                'is_verified' => true,
                'verified_at' => now(),
                'votes_count' => 8,
                'comments_count' => 1,
                'priority' => 6,
            ],
            [
                'user_id' => null, // Anonymous report
                'title' => 'Illegal dumping site',
                'description' => 'People are dumping construction waste in this area, creating an eyesore and health hazard.',
                'category_id' => $categories->where('slug', 'waste-management')->first()->id,
                'latitude' => 3.8445,
                'longitude' => 11.4987,
                'address' => 'Near Mvan, Yaoundé',
                'status' => 'pending',
                'reporter_name' => 'Anonymous',
                'reporter_email' => null,
                'is_verified' => true,
                'verified_at' => now(),
                'votes_count' => 19,
                'comments_count' => 4,
                'priority' => 8,
            ],
            [
                'user_id' => $users->random()->id ?? null,
                'title' => 'Damaged road sign',
                'description' => 'The stop sign at this intersection is bent and barely visible, creating a safety hazard.',
                'category_id' => $categories->where('slug', 'public-safety')->first()->id,
                'latitude' => 3.8556,
                'longitude' => 11.5123,
                'address' => 'Carrefour Nlongkak, Yaoundé',
                'status' => 'in_progress',
                'is_verified' => true,
                'verified_at' => now()->subDays(3),
                'votes_count' => 14,
                'comments_count' => 3,
                'priority' => 7,
            ],
            [
                'user_id' => $users->random()->id ?? null,
                'title' => 'Cracked sidewalk causing trip hazard',
                'description' => 'The sidewalk has several large cracks that are dangerous for pedestrians, especially elderly people.',
                'category_id' => $categories->where('slug', 'road-issues')->first()->id,
                'latitude' => 3.8634,
                'longitude' => 11.5156,
                'address' => 'Boulevard du 20 Mai, Yaoundé',
                'status' => 'pending',
                'is_verified' => true,
                'verified_at' => now(),
                'votes_count' => 6,
                'comments_count' => 1,
                'priority' => 5,
            ],
        ];

        foreach ($issues as $issueData) {
            Issue::create($issueData);
        }
    }
}
