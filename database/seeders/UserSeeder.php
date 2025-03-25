<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            User::insert([
                [
                    'id_branch_user' => 1,
                    'username' => 'admin_jakarta',
                    'password' => Hash::make('password123'),
                    'fullname_user' => 'Budi Santoso',
                    'role_user' => 'admin',
                    'gender_user' => 'male',
                    'phone_user' => '081234567891',
                    'address_user' => 'Jl. Sudirman No.10, Jakarta',
                    'is_active_user' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_user' => 2,
                    'username' => 'kurir_surabaya',
                    'password' => Hash::make('password123'),
                    'fullname_user' => 'Rizky Pratama',
                    'role_user' => 'kurir',
                    'gender_user' => 'male',
                    'phone_user' => '081234567892',
                    'address_user' => 'Jl. Basuki Rahmat No.20, Surabaya',
                    'is_active_user' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_user' => 3,
                    'username' => 'admin_bandung',
                    'password' => Hash::make('password123'),
                    'fullname_user' => 'Siti Nurhaliza',
                    'role_user' => 'admin',
                    'gender_user' => 'female',
                    'phone_user' => '081234567893',
                    'address_user' => 'Jl. Asia Afrika No.30, Bandung',
                    'is_active_user' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_user' => null,
                    'username' => 'owner_ahmad',
                    'password' => Hash::make('password123'),
                    'fullname_user' => 'Ahmad Fauzi',
                    'role_user' => 'owner',
                    'gender_user' => 'male',
                    'phone_user' => '081234567894',
                    'address_user' => 'Jl. Merdeka No.40, Yogyakarta',
                    'is_active_user' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_user' => 4,
                    'username' => 'kurir_medan',
                    'password' => Hash::make('password123'),
                    'fullname_user' => 'Indra Kusuma',
                    'role_user' => 'kurir',
                    'gender_user' => 'male',
                    'phone_user' => '081234567895',
                    'address_user' => 'Jl. Sisingamangaraja No.50, Medan',
                    'is_active_user' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_user' => null,
                    'username' => 'owner_shiva',
                    'password' => Hash::make('password123'),
                    'fullname_user' => 'Nurul Aini',
                    'role_user' => 'owner',
                    'gender_user' => 'female',
                    'phone_user' => '081234567896',
                    'address_user' => 'Jl. Diponegoro No.60, Malang',
                    'is_active_user' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_user' => 5,
                    'username' => 'admin_semarang',
                    'password' => Hash::make('password123'),
                    'fullname_user' => 'Rahmad Hidayat',
                    'role_user' => 'admin',
                    'gender_user' => 'male',
                    'phone_user' => '081234567897',
                    'address_user' => 'Jl. Pandanaran No.70, Semarang',
                    'is_active_user' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_user' => 2,
                    'username' => 'kurir_surabaya2',
                    'password' => Hash::make('password123'),
                    'fullname_user' => 'Dewi Sartika',
                    'role_user' => 'kurir',
                    'gender_user' => 'female',
                    'phone_user' => '081234567898',
                    'address_user' => 'Jl. Tunjungan No.80, Surabaya',
                    'is_active_user' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_user' => 3,
                    'username' => 'admin_bandung2',
                    'password' => Hash::make('password123'),
                    'fullname_user' => 'Andi Saputra',
                    'role_user' => 'admin',
                    'gender_user' => 'male',
                    'phone_user' => '081234567899',
                    'address_user' => 'Jl. Cihampelas No.90, Bandung',
                    'is_active_user' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_user' => null,
                    'username' => 'owner_fadli',
                    'password' => Hash::make('password123'),
                    'fullname_user' => 'Fadli Ramadhan',
                    'role_user' => 'owner',
                    'gender_user' => 'male',
                    'phone_user' => '081234567900',
                    'address_user' => 'Jl. Sudirman No.100, Jakarta',
                    'is_active_user' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            Log::info('Data user berhasil disimpan');
        } catch (\Exception $error) {
            Log::error('Data user gagal disimpan', ['error' => $error->getMessage()]);
        }
    }
}