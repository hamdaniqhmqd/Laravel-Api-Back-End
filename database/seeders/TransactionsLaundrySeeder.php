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
                    'id_user_transaction_laundry' => 1,
                    'id_branch_transaction_laundry' => 1,
                    'time_transaction_laundry' => '09:30:00',
                    'name_client_transaction_laundry' => 'Budi Santoso',
                    'status_transaction_laundry' => 'pending',
                    'notes_transaction_laundry' => 'Minta parfum tambahan',
                    'total_weight_transaction_laundry' => 3.5,
                    'total_price_transaction_laundry' => 35000,
                    'cash_transaction_laundry' => 50000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(3),
                    'last_date_transaction_laundry' => Carbon::now()->addDays(2),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],

                [
                    'id_user_transaction_laundry' => 2,
                    'id_branch_transaction_laundry' => 2,
                    'time_transaction_laundry' => '10:15:00',
                    'name_client_transaction_laundry' => 'Siti Aisyah',
                    'status_transaction_laundry' => 'in_progress',
                    'notes_transaction_laundry' => NULL,
                    'total_weight_transaction_laundry' => 5.2,
                    'total_price_transaction_laundry' => 52000,
                    'cash_transaction_laundry' => 60000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(2),
                    'last_date_transaction_laundry' => Carbon::now()->addDays(3),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],

                [
                    'id_user_transaction_laundry' => 3,
                    'id_branch_transaction_laundry' => 3,
                    'time_transaction_laundry' => '11:00:00',
                    'name_client_transaction_laundry' => 'Andi Wijaya',
                    'status_transaction_laundry' => 'completed',
                    'notes_transaction_laundry' => 'Jangan terlalu banyak pelembut',
                    'total_weight_transaction_laundry' => 4.8,
                    'total_price_transaction_laundry' => 48000,
                    'cash_transaction_laundry' => 50000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(5),
                    'last_date_transaction_laundry' => Carbon::now()->subDays(1),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],

                [
                    'id_user_transaction_laundry' => 4,
                    'id_branch_transaction_laundry' => 4,
                    'time_transaction_laundry' => '14:30:00',
                    'name_client_transaction_laundry' => 'Rina Puspita',
                    'status_transaction_laundry' => 'cancelled',
                    'notes_transaction_laundry' => 'Pembatalan karena waktu terlalu lama',
                    'total_weight_transaction_laundry' => 3.0,
                    'total_price_transaction_laundry' => 30000,
                    'cash_transaction_laundry' => 0,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(1),
                    'last_date_transaction_laundry' => Carbon::now()->addDays(2),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],

                [
                    'id_user_transaction_laundry' => 5,
                    'id_branch_transaction_laundry' => 5,
                    'time_transaction_laundry' => '15:45:00',
                    'name_client_transaction_laundry' => 'Dewi Lestari',
                    'status_transaction_laundry' => 'pending',
                    'notes_transaction_laundry' => 'Pisahkan pakaian putih',
                    'total_weight_transaction_laundry' => 6.2,
                    'total_price_transaction_laundry' => 62000,
                    'cash_transaction_laundry' => 70000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(2),
                    'last_date_transaction_laundry' => Carbon::now()->addDays(4),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],

                [
                    'id_user_transaction_laundry' => 6,
                    'id_branch_transaction_laundry' => 1,
                    'time_transaction_laundry' => '16:00:00',
                    'name_client_transaction_laundry' => 'Eko Prasetyo',
                    'status_transaction_laundry' => 'completed',
                    'notes_transaction_laundry' => NULL,
                    'total_weight_transaction_laundry' => 4.0,
                    'total_price_transaction_laundry' => 40000,
                    'cash_transaction_laundry' => 50000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(7),
                    'last_date_transaction_laundry' => Carbon::now()->subDays(2),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],

                [
                    'id_user_transaction_laundry' => 7,
                    'id_branch_transaction_laundry' => 2,
                    'time_transaction_laundry' => '08:45:00',
                    'name_client_transaction_laundry' => 'Lia Kusuma',
                    'status_transaction_laundry' => 'in_progress',
                    'notes_transaction_laundry' => 'Gunakan deterjen hypoallergenic',
                    'total_weight_transaction_laundry' => 5.5,
                    'total_price_transaction_laundry' => 55000,
                    'cash_transaction_laundry' => 60000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(3),
                    'last_date_transaction_laundry' => Carbon::now()->addDays(2),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],

                [
                    'id_user_transaction_laundry' => 8,
                    'id_branch_transaction_laundry' => 3,
                    'time_transaction_laundry' => '13:15:00',
                    'name_client_transaction_laundry' => 'Bambang Sugiarto',
                    'status_transaction_laundry' => 'pending',
                    'notes_transaction_laundry' => NULL,
                    'total_weight_transaction_laundry' => 3.7,
                    'total_price_transaction_laundry' => 37000,
                    'cash_transaction_laundry' => 50000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(2),
                    'last_date_transaction_laundry' => Carbon::now()->addDays(3),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],

                [
                    'id_user_transaction_laundry' => 9,
                    'id_branch_transaction_laundry' => 4,
                    'time_transaction_laundry' => '17:30:00',
                    'name_client_transaction_laundry' => 'Sari Widodo',
                    'status_transaction_laundry' => 'completed',
                    'notes_transaction_laundry' => 'Jemur ekstra lama',
                    'total_weight_transaction_laundry' => 4.5,
                    'total_price_transaction_laundry' => 45000,
                    'cash_transaction_laundry' => 50000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(6),
                    'last_date_transaction_laundry' => Carbon::now()->subDays(2),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],

                [
                    'id_user_transaction_laundry' => 10,
                    'id_branch_transaction_laundry' => 5,
                    'time_transaction_laundry' => '12:00:00',
                    'name_client_transaction_laundry' => 'Yuli Rahman',
                    'status_transaction_laundry' => 'in_progress',
                    'notes_transaction_laundry' => NULL,
                    'total_weight_transaction_laundry' => 6.0,
                    'total_price_transaction_laundry' => 60000,
                    'cash_transaction_laundry' => 70000,
                    'is_active_transaction_laundry' => 'active',
                    'first_date_transaction_laundry' => Carbon::now()->subDays(4),
                    'last_date_transaction_laundry' => Carbon::now()->addDays(2),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            Log::info('Data transaksi laundry berhasil disimpan');
        } catch (\Exception $error) {
            Log::error('Gagal menyimpan data transaksi laundry: ' . $error->getMessage());
        }
    }
}
