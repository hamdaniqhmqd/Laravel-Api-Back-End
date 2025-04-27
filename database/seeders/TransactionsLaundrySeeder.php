<?php

namespace Database\Seeders;

use App\Models\Transaction_Laundry;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class TransactionsLaundrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            Transaction_Laundry::insert([
                [
                    'id_user_transaction_laundry' => 2,
                    'id_branch_transaction_laundry' => 2,
                    'name_client_transaction_laundry' => 'Budi Santoso',
                    'status_transaction_laundry' => 'pending',
                    'notes_transaction_laundry' => 'Minta parfum tambahan',
                    'total_weight_transaction_laundry' => 3.5,
                    'total_price_transaction_laundry' => 35000,
                    'count_item_laundry_transaction_laundry' => 1,
                    'promo_transaction_laundry' => 0,
                    'additional_cost_transaction_laundry' => 0,
                    'total_transaction_laundry' => 35000,
                    'cash_transaction_laundry' => 50000,
                    'change_money_transaction_laundry' => 15000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(3),
                    'last_date_transaction_laundry' => null,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_user_transaction_laundry' => 2,
                    'id_branch_transaction_laundry' => 2,
                    'name_client_transaction_laundry' => 'Siti Aisyah',
                    'status_transaction_laundry' => 'in_progress',
                    'notes_transaction_laundry' => null,
                    'total_weight_transaction_laundry' => 5.2,
                    'total_price_transaction_laundry' => 52000,
                    'count_item_laundry_transaction_laundry' => 2,
                    'promo_transaction_laundry' => 5000,
                    'additional_cost_transaction_laundry' => 2000,
                    'total_transaction_laundry' => 49000,
                    'cash_transaction_laundry' => 60000,
                    'change_money_transaction_laundry' => 11000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(2),
                    'last_date_transaction_laundry' => null,
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_user_transaction_laundry' => 5,
                    'id_branch_transaction_laundry' => 4,
                    'name_client_transaction_laundry' => 'Joko Widodo',
                    'status_transaction_laundry' => 'completed',
                    'notes_transaction_laundry' => 'Jemput ke rumah',
                    'total_weight_transaction_laundry' => 7.1,
                    'total_price_transaction_laundry' => 71000,
                    'count_item_laundry_transaction_laundry' => 3,
                    'promo_transaction_laundry' => 10000,
                    'additional_cost_transaction_laundry' => 5000,
                    'total_transaction_laundry' => 66000,
                    'cash_transaction_laundry' => 70000,
                    'change_money_transaction_laundry' => 4000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(5),
                    'last_date_transaction_laundry' => Carbon::now(),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_user_transaction_laundry' => 8,
                    'id_branch_transaction_laundry' => 2,
                    'name_client_transaction_laundry' => 'Dewi Lestari',
                    'status_transaction_laundry' => 'cancelled',
                    'notes_transaction_laundry' => 'Batal, pelanggan membatalkan pesanan',
                    'total_weight_transaction_laundry' => 4.0,
                    'total_price_transaction_laundry' => 40000,
                    'count_item_laundry_transaction_laundry' => 2,
                    'promo_transaction_laundry' => 0,
                    'additional_cost_transaction_laundry' => 0,
                    'total_transaction_laundry' => 0,
                    'cash_transaction_laundry' => 0,
                    'change_money_transaction_laundry' => 0,
                    'is_active_transaction_laundry' => 'inactive',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(1),
                    'last_date_transaction_laundry' => Carbon::now()->addDays(1),
                    'deleted_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_user_transaction_laundry' => 8,
                    'id_branch_transaction_laundry' => 2,
                    'name_client_transaction_laundry' => 'Agus Riyadi',
                    'status_transaction_laundry' => 'completed',
                    'notes_transaction_laundry' => 'Setrika rapi',
                    'total_weight_transaction_laundry' => 6.8,
                    'total_price_transaction_laundry' => 68000,
                    'count_item_laundry_transaction_laundry' => 4,
                    'promo_transaction_laundry' => 0,
                    'additional_cost_transaction_laundry' => 5000,
                    'total_transaction_laundry' => 73000,
                    'cash_transaction_laundry' => 75000,
                    'change_money_transaction_laundry' => 2000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(4),
                    'last_date_transaction_laundry' => Carbon::now()->addDays(1),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            Log::info('10 data transaksi laundry berhasil disimpan');
        } catch (\Exception $error) {
            Log::error('Gagal menyimpan data transaksi laundry: ' . $error->getMessage());
        }
    }
}
