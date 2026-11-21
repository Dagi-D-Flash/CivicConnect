<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@civicconnect.com',
        ]);

        // Seed all data in order
        $this->call([
            CategorySeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            IssueSeeder::class,
        ]);
    }
}
