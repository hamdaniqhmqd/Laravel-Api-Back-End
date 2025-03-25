<?php

namespace Database\Seeders;

use App\Models\Laundry_Item;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class LaundryItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        try {
            Laundry_Item::insert([
                [
                    'id_branch_laundry_item' => 1,
                    'name_laundry_item' => 'CS EXPRESS',
                    'price_laundry_item' => 8000,
                    'time_laundry__item' => '00:30:00',
                    'description_laundry_item' => 'Cuci Setrika Express 30 menit',
                    'is_active_laundry_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_laundry_item' => 2,
                    'name_laundry_item' => 'CS KILAT',
                    'price_laundry_item' => 6000,
                    'time_laundry__item' => '01:00:00',
                    'description_laundry_item' => 'Cuci Setrika Kilat 1 jam',
                    'is_active_laundry_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_laundry_item' => 3,
                    'name_laundry_item' => 'CS SEMI',
                    'price_laundry_item' => 5000,
                    'time_laundry__item' => '02:00:00',
                    'description_laundry_item' => 'Cuci Setrika Semi 2 jam',
                    'is_active_laundry_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_laundry_item' => 4,
                    'name_laundry_item' => 'CS BIASA',
                    'price_laundry_item' => 4000,
                    'time_laundry__item' => null,
                    'description_laundry_item' => 'Cuci Setrika Biasa',
                    'is_active_laundry_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_laundry_item' => 5,
                    'name_laundry_item' => 'SPREI + SAWAL',
                    'price_laundry_item' => 5000,
                    'time_laundry__item' => null,
                    'description_laundry_item' => 'Cuci sprei dan sawal per kg',
                    'is_active_laundry_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_laundry_item' => 1,
                    'name_laundry_item' => 'BED COVER BESAR',
                    'price_laundry_item' => 20000,
                    'time_laundry__item' => null,
                    'description_laundry_item' => 'Cuci Bed Cover Besar per biji',
                    'is_active_laundry_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_laundry_item' => 2,
                    'name_laundry_item' => 'KARPET TEBAL',
                    'price_laundry_item' => 8000,
                    'time_laundry__item' => null,
                    'description_laundry_item' => 'Cuci karpet tebal per meter',
                    'is_active_laundry_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_laundry_item' => 3,
                    'name_laundry_item' => 'KARPET SEDANG',
                    'price_laundry_item' => 6000,
                    'time_laundry__item' => null,
                    'description_laundry_item' => 'Cuci karpet sedang per meter',
                    'is_active_laundry_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_laundry_item' => 4,
                    'name_laundry_item' => 'KORDEN TEBAL',
                    'price_laundry_item' => 8000,
                    'time_laundry__item' => null,
                    'description_laundry_item' => 'Cuci korden tebal per meter',
                    'is_active_laundry_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_laundry_item' => 5,
                    'name_laundry_item' => 'KORDEN TIPIS',
                    'price_laundry_item' => 4000,
                    'time_laundry__item' => null,
                    'description_laundry_item' => 'Cuci korden tipis per meter',
                    'is_active_laundry_item' => 'active',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            Log::info('Data item laundry berhasil disimpan');
        } catch (\Exception $error) {
            Log::error('Data item laundry gagal disimpan', ['error' => $error->getMessage()]);
        }
    }
}
