<?php

namespace Database\Seeders;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            Client::insert([
                [
                    'id_branch_client' => 1,
                    'name_client' => 'John Doe',
                    'address_client' => '123 Main St, City A',
                    'phone_client' => '081234567890',
                    'is_active_client' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_client' => 2,
                    'name_client' => 'Jane Smith',
                    'address_client' => '456 Maple Ave, City B',
                    'phone_client' => '081298765432',
                    'is_active_client' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_client' => 3,
                    'name_client' => 'Michael Johnson',
                    'address_client' => '789 Oak St, City C',
                    'phone_client' => '081377788899',
                    'is_active_client' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_client' => 4,
                    'name_client' => 'Emily Davis',
                    'address_client' => '101 Pine Rd, City D',
                    'phone_client' => '081366655544',
                    'is_active_client' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_client' => 5,
                    'name_client' => 'Robert Wilson',
                    'address_client' => '202 Cedar Ln, City E',
                    'phone_client' => '081355522211',
                    'is_active_client' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_client' => 1,
                    'name_client' => 'David Martinez',
                    'address_client' => '303 Birch Blvd, City F',
                    'phone_client' => '081344433322',
                    'is_active_client' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_client' => 2,
                    'name_client' => 'Sarah Lee',
                    'address_client' => '404 Willow St, City G',
                    'phone_client' => '081322211100',
                    'is_active_client' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_client' => 3,
                    'name_client' => 'Daniel Brown',
                    'address_client' => '505 Elm Dr, City H',
                    'phone_client' => '081399988877',
                    'is_active_client' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_client' => 4,
                    'name_client' => 'Laura White',
                    'address_client' => '606 Spruce Ct, City I',
                    'phone_client' => '081388877766',
                    'is_active_client' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_client' => 5,
                    'name_client' => 'James Anderson',
                    'address_client' => '707 Redwood Rd, City J',
                    'phone_client' => '081377766655',
                    'is_active_client' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            Log::info('Data client berhasil disimpan');
        } catch (\Exception $error) {
            Log::error('Data client gagal disimpan', ['error' => $error->getMessage()]);
        }
    }
}
