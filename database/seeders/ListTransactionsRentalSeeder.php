<?php

namespace Database\Seeders;

use App\Models\List_Transaction_Rental;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ListTransactionsRentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            List_Transaction_Rental::insert([
                [
                    'id_rental_transaction' => 1,
                    'id_item_rental' => 2,
                    'status_list_transaction_rental' => 'rented',
                    'condition_list_transaction_rental' => 'clean',
                    'note_list_transaction_rental' => 'Rental for wedding event.',
                    'price_list_transaction_rental' => 500000,
                    'weight_list_transaction_rental' => 15.5,
                    'is_active_list_transaction_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_rental_transaction' => 2,
                    'id_item_rental' => 5,
                    'status_list_transaction_rental' => 'returned',
                    'condition_list_transaction_rental' => 'dirty',
                    'note_list_transaction_rental' => 'Used in outdoor event.',
                    'price_list_transaction_rental' => 750000,
                    'weight_list_transaction_rental' => 20.3,
                    'is_active_list_transaction_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_rental_transaction' => 3,
                    'id_item_rental' => 7,
                    'status_list_transaction_rental' => 'cancelled',
                    'condition_list_transaction_rental' => 'clean',
                    'note_list_transaction_rental' => 'Customer cancelled due to rain.',
                    'price_list_transaction_rental' => 600000,
                    'weight_list_transaction_rental' => 10.2,
                    'is_active_list_transaction_rental' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_rental_transaction' => 4,
                    'id_item_rental' => 1,
                    'status_list_transaction_rental' => 'rented',
                    'condition_list_transaction_rental' => 'clean',
                    'note_list_transaction_rental' => 'For corporate event.',
                    'price_list_transaction_rental' => 1200000,
                    'weight_list_transaction_rental' => 25.0,
                    'is_active_list_transaction_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_rental_transaction' => 5,
                    'id_item_rental' => 3,
                    'status_list_transaction_rental' => 'returned',
                    'condition_list_transaction_rental' => 'damaged',
                    'note_list_transaction_rental' => 'Minor scratches on surface.',
                    'price_list_transaction_rental' => 950000,
                    'weight_list_transaction_rental' => 18.7,
                    'is_active_list_transaction_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_rental_transaction' => 1,
                    'id_item_rental' => 6,
                    'status_list_transaction_rental' => 'rented',
                    'condition_list_transaction_rental' => 'clean',
                    'note_list_transaction_rental' => 'For photo shoot.',
                    'price_list_transaction_rental' => 850000,
                    'weight_list_transaction_rental' => 12.5,
                    'is_active_list_transaction_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_rental_transaction' => 3,
                    'id_item_rental' => 4,
                    'status_list_transaction_rental' => 'returned',
                    'condition_list_transaction_rental' => 'dirty',
                    'note_list_transaction_rental' => 'Used in muddy area.',
                    'price_list_transaction_rental' => 720000,
                    'weight_list_transaction_rental' => 14.8,
                    'is_active_list_transaction_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_rental_transaction' => 4,
                    'id_item_rental' => 8,
                    'status_list_transaction_rental' => 'rented',
                    'condition_list_transaction_rental' => 'clean',
                    'note_list_transaction_rental' => 'VIP customer order.',
                    'price_list_transaction_rental' => 1500000,
                    'weight_list_transaction_rental' => 22.5,
                    'is_active_list_transaction_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_rental_transaction' => 2,
                    'id_item_rental' => 9,
                    'status_list_transaction_rental' => 'returned',
                    'condition_list_transaction_rental' => 'clean',
                    'note_list_transaction_rental' => 'Well maintained.',
                    'price_list_transaction_rental' => 650000,
                    'weight_list_transaction_rental' => 9.5,
                    'is_active_list_transaction_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
                [
                    'id_rental_transaction' => 5,
                    'id_item_rental' => 10,
                    'status_list_transaction_rental' => 'cancelled',
                    'condition_list_transaction_rental' => 'clean',
                    'note_list_transaction_rental' => 'Customer rescheduled.',
                    'price_list_transaction_rental' => 880000,
                    'weight_list_transaction_rental' => 16.3,
                    'is_active_list_transaction_rental' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ],
            ]);

            Log::info('Data List Transaksi Rental berhasil disimpan');
        } catch (\Exception $error) {
            Log::error('Gagal menyimpan data List Transaksi Rental: ' . $error->getMessage());
        }
    }
}
