<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            $this->call([
                BranchSeeder::class,
                UserSeeder::class,
            ]);

            Log::info('Database seeded successfully'); //code...
        } catch (\Exception $error) {
            Log::error("Database seeding failed: " . $error->getMessage());
        }
    }
}