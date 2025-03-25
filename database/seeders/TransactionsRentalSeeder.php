<?php

namespace Database\Seeders;

use App\Models\Transaction_Rental;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class TransactionsRentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            Transaction_Rental::insert([
                [
                    'id_kurir_transaction_rental' => 1,
                    'id_branch_transaction_rental' => 1,
                    'id_client_transaction_rental' => 1,
                    'recipient_name_transaction_rental' => 'Ahmad Hamdani',
                    'status_transaction_rental' => 'waiting for approval',
                    'total_weight_transaction_rental' => 2.5,
                    'total_pcs_transaction_rental' => 3,
                    'promo_transaction_rental' => 5000,
                    'additional_cost_transaction_rental' => 10000,
                    'total_price_transaction_rental' => 50000,
                    'notes_transaction_laundry' => 'Jaga kebersihan barang.',
                    'is_active_transaction_rental' => 'active',
                    'first_date_transaction_rental' => Carbon::now()->subDays(3),
                    'last_date_transaction_rental' => Carbon::now(),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_kurir_transaction_rental' => 2,
                    'id_branch_transaction_rental' => 2,
                    'id_client_transaction_rental' => 2,
                    'recipient_name_transaction_rental' => 'Budi Santoso',
                    'status_transaction_rental' => 'approved',
                    'total_weight_transaction_rental' => 5.2,
                    'total_pcs_transaction_rental' => 7,
                    'promo_transaction_rental' => 0,
                    'additional_cost_transaction_rental' => 5000,
                    'total_price_transaction_rental' => 75000,
                    'notes_transaction_laundry' => 'Pastikan dikeringkan dengan baik.',
                    'is_active_transaction_rental' => 'active',
                    'first_date_transaction_rental' => Carbon::now()->subDays(5),
                    'last_date_transaction_rental' => Carbon::now()->subDays(1),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_kurir_transaction_rental' => 3,
                    'id_branch_transaction_rental' => 3,
                    'id_client_transaction_rental' => 3,
                    'recipient_name_transaction_rental' => 'Siti Aisyah',
                    'status_transaction_rental' => 'out',
                    'total_weight_transaction_rental' => 3.8,
                    'total_pcs_transaction_rental' => 5,
                    'promo_transaction_rental' => 2000,
                    'additional_cost_transaction_rental' => 0,
                    'total_price_transaction_rental' => 60000,
                    'notes_transaction_laundry' => 'Simpan di tempat kering.',
                    'is_active_transaction_rental' => 'active',
                    'first_date_transaction_rental' => Carbon::now()->subDays(7),
                    'last_date_transaction_rental' => Carbon::now()->subDays(2),
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_kurir_transaction_rental' => 1,
                    'id_branch_transaction_rental' => 1,
                    'id_client_transaction_rental' => 1,
                    'recipient_name_transaction_rental' => 'Budi Santoso',
                    'status_transaction_rental' => 'approved',
                    'total_weight_transaction_rental' => 12.5,
                    'total_pcs_transaction_rental' => 5,
                    'promo_transaction_rental' => 10000,
                    'additional_cost_transaction_rental' => 5000,
                    'total_price_transaction_rental' => 150000,
                    'notes_transaction_laundry' => 'Pesanan harus bersih sebelum dikembalikan.',
                    'is_active_transaction_rental' => 'active',
                    'first_date_transaction_rental' => '2025-03-01',
                    'last_date_transaction_rental' => '2025-03-07',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_kurir_transaction_rental' => 2,
                    'id_branch_transaction_rental' => 2,
                    'id_client_transaction_rental' => 2,
                    'recipient_name_transaction_rental' => 'Siti Rahmawati',
                    'status_transaction_rental' => 'out',
                    'total_weight_transaction_rental' => 8.2,
                    'total_pcs_transaction_rental' => 3,
                    'promo_transaction_rental' => 0,
                    'additional_cost_transaction_rental' => 7000,
                    'total_price_transaction_rental' => 110000,
                    'notes_transaction_laundry' => 'Barang akan diambil pukul 10.00.',
                    'is_active_transaction_rental' => 'active',
                    'first_date_transaction_rental' => '2025-03-02',
                    'last_date_transaction_rental' => '2025-03-06',
                    'deleted_at' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            Log::info('Data Transaksi Rental berhasil disimpan');
        } catch (\Exception $error) {
            Log::error('Gagal menyimpan data Transaksi Rental: ' . $error->getMessage());
        }
    }
}
