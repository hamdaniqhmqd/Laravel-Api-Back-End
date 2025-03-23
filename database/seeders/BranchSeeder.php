<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang'];

        for ($i = 1; $i <= 5; $i++) {
            Branch::create([
                'name_branch' => 'Branch ' . $i,
                'city_branch' => $cities[array_rand($cities)],
                'address_branch' => 'Alamat cabang ' . $i,
                'is_active_branch' => rand(0, 1) ? 'active' : 'inactive',
            ]);
        }
    }
}