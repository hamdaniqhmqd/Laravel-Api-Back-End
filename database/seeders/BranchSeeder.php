<?php

namespace Database\Seeders;

use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            Branch::insert([
                [
                    'name_branch' => 'Branch Jakarta Pusat',
                    'city_branch' => 'Jakarta',
                    'address_branch' => 'Jl. MH Thamrin No.10, Jakarta Pusat',
                    'is_active_branch' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),

                ],
                [
                    'name_branch' => 'Branch Surabaya Timur',
                    'city_branch' => 'Surabaya',
                    'address_branch' => 'Jl. Raya Darmo No.25, Surabaya',
                    'is_active_branch' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name_branch' => 'Branch Bandung Utara',
                    'city_branch' => 'Bandung',
                    'address_branch' => 'Jl. Setiabudi No.45, Bandung',
                    'is_active_branch' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name_branch' => 'Branch Medan Kota',
                    'city_branch' => 'Medan',
                    'address_branch' => 'Jl. Gatot Subroto No.100, Medan',
                    'is_active_branch' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'name_branch' => 'Branch Semarang Barat',
                    'city_branch' => 'Semarang',
                    'address_branch' => 'Jl. Pemuda No.30, Semarang',
                    'is_active_branch' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            Log::info('Data branch berhasil disimpan');
        } catch (\Exception $error) {
            Log::error('Gagal menyimpan data branch: ' . $error->getMessage());
        }
    }
}
