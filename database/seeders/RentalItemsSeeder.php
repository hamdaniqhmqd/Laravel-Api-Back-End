<?php

namespace Database\Seeders;

use App\Models\Rental_Item;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class RentalItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            Rental_Item::insert([
                [
                    'id_branch_rental_item' => 1,
                    'number_rental_item' => 'RTL-001',
                    'name_rental_item' => 'Handuk Kecil',
                    'price_rental_item' => 5000,
                    'status_rental_item' => 'available',
                    'condition_rental_item' => 'clean',
                    'description_rental_item' => 'Handuk kecil nyaman dipakai',
                    'is_active_rental_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_rental_item' => 1,
                    'number_rental_item' => 'RTL-002',
                    'name_rental_item' => 'Handuk Besar',
                    'price_rental_item' => 8000,
                    'status_rental_item' => 'rented',
                    'condition_rental_item' => 'clean',
                    'description_rental_item' => 'Handuk besar tebal',
                    'is_active_rental_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_rental_item' => 2,
                    'number_rental_item' => 'RTL-003',
                    'name_rental_item' => 'Handuk Kecil',
                    'price_rental_item' => 6000,
                    'status_rental_item' => 'available',
                    'condition_rental_item' => 'clean',
                    'description_rental_item' => 'Handuk kecil cabang 2',
                    'is_active_rental_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_rental_item' => 2,
                    'number_rental_item' => 'RTL-004',
                    'name_rental_item' => 'Handuk Besar',
                    'price_rental_item' => 9000,
                    'status_rental_item' => 'maintenance',
                    'condition_rental_item' => 'dirty',
                    'description_rental_item' => 'Handuk besar cabang 2 perlu dicuci',
                    'is_active_rental_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_rental_item' => 3,
                    'number_rental_item' => 'RTL-005',
                    'name_rental_item' => 'Handuk Kecil',
                    'price_rental_item' => 5500,
                    'status_rental_item' => 'available',
                    'condition_rental_item' => 'clean',
                    'description_rental_item' => 'Handuk kecil lembut',
                    'is_active_rental_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_rental_item' => 3,
                    'number_rental_item' => 'RTL-006',
                    'name_rental_item' => 'Handuk Besar',
                    'price_rental_item' => 8500,
                    'status_rental_item' => 'rented',
                    'condition_rental_item' => 'clean',
                    'description_rental_item' => 'Handuk besar cabang 3',
                    'is_active_rental_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_rental_item' => 4,
                    'number_rental_item' => 'RTL-007',
                    'name_rental_item' => 'Handuk Kecil',
                    'price_rental_item' => 5200,
                    'status_rental_item' => 'available',
                    'condition_rental_item' => 'clean',
                    'description_rental_item' => 'Handuk kecil harga murah',
                    'is_active_rental_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_rental_item' => 4,
                    'number_rental_item' => 'RTL-008',
                    'name_rental_item' => 'Handuk Besar',
                    'price_rental_item' => 7800,
                    'status_rental_item' => 'maintenance',
                    'condition_rental_item' => 'damaged',
                    'description_rental_item' => 'Handuk besar rusak perlu diganti',
                    'is_active_rental_item' => 'inactive',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_rental_item' => 5,
                    'number_rental_item' => 'RTL-009',
                    'name_rental_item' => 'Handuk Kecil',
                    'price_rental_item' => 5700,
                    'status_rental_item' => 'available',
                    'condition_rental_item' => 'clean',
                    'description_rental_item' => 'Handuk kecil ekstra lembut',
                    'is_active_rental_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_rental_item' => 5,
                    'number_rental_item' => 'RTL-010',
                    'name_rental_item' => 'Handuk Besar',
                    'price_rental_item' => 8700,
                    'status_rental_item' => 'rented',
                    'condition_rental_item' => 'clean',
                    'description_rental_item' => 'Handuk besar cabang 5',
                    'is_active_rental_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            Log::info('Data rental item berhasil disimpan');
        } catch (\Exception $error) {
            Log::error('Data rental item gagal disimpan', ['error' => $error->getMessage()]);
        }
    }
}
