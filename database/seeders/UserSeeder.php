<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create regular users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'alias' => null,
                'is_anonymous' => false,
                'phone' => '+237 670 123 456',
                'bio' => 'Active community member who reports issues regularly.',
                'reputation_points' => 150,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'alias' => 'CommunityHelper',
                'is_anonymous' => true,
                'phone' => '+237 670 123 457',
                'bio' => 'Prefers to stay anonymous while helping the community.',
                'reputation_points' => 85,
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'alias' => null,
                'is_anonymous' => false,
                'phone' => '+237 670 123 458',
                'bio' => 'Concerned citizen focused on road safety.',
                'reputation_points' => 220,
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
