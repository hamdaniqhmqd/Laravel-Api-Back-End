<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['admin', 'owner', 'kurir'];
        $genders = ['male', 'female'];

        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'id_branch_user' => rand(1, 5), // Sesuaikan dengan jumlah branch yang tersedia
                'username' => 'user' . $i,
                'password' => Hash::make('password123'),
                'fullname_user' => 'User ' . $i,
                'role_user' => $roles[array_rand($roles)],
                'gender_user' => $genders[array_rand($genders)],
                'phone_user' => '08123456789' . $i,
                'address_user' => 'Alamat user ' . $i,
                'is_active_user' => rand(0, 1) ? 'active' : 'inactive',
            ]);
        }
    }
}