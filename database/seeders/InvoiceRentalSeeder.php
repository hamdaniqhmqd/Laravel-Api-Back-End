<?php

namespace Database\Seeders;

use App\Models\Invoice_Rental;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class InvoiceRentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            Invoice_Rental::insert([
                [
                    'id_branch_invoice' => 1,
                    'id_client_invoice' => 2,
                    'notes_invoice_rental' => 'Sewa harian paket ringan',
                    'time_invoice_rental' => '09:00:00',
                    'total_weight_invoice_rental' => 120.5,
                    'price_invoice_rental' => 3500,
                    'promo_invoice_rental' => 5000,
                    'additional_cost_invoice_rental' => 2000,
                    'total_price_invoice_rental' => 418750,
                    'is_active_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_invoice' => 2,
                    'id_client_invoice' => 5,
                    'notes_invoice_rental' => 'Sewa bulanan full service',
                    'time_invoice_rental' => '14:30:00',
                    'total_weight_invoice_rental' => 300.0,
                    'price_invoice_rental' => 4000,
                    'promo_invoice_rental' => 10000,
                    'additional_cost_invoice_rental' => 5000,
                    'total_price_invoice_rental' => 1215000,
                    'is_active_invoice_rental' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_invoice' => 3,
                    'id_client_invoice' => 7,
                    'notes_invoice_rental' => 'Sewa mingguan dengan promo',
                    'time_invoice_rental' => '10:15:00',
                    'total_weight_invoice_rental' => 150.0,
                    'price_invoice_rental' => 3200,
                    'promo_invoice_rental' => 7000,
                    'additional_cost_invoice_rental' => 3000,
                    'total_price_invoice_rental' => 476000,
                    'is_active_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_invoice' => 4,
                    'id_client_invoice' => 1,
                    'notes_invoice_rental' => 'Kebutuhan event lokal',
                    'time_invoice_rental' => '08:45:00',
                    'total_weight_invoice_rental' => 250.0,
                    'price_invoice_rental' => 3800,
                    'promo_invoice_rental' => 0,
                    'additional_cost_invoice_rental' => 8000,
                    'total_price_invoice_rental' => 958000,
                    'is_active_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_invoice' => 5,
                    'id_client_invoice' => 4,
                    'notes_invoice_rental' => 'Paket reguler bulanan',
                    'time_invoice_rental' => '11:30:00',
                    'total_weight_invoice_rental' => 100.0,
                    'price_invoice_rental' => 3000,
                    'promo_invoice_rental' => 3000,
                    'additional_cost_invoice_rental' => 1500,
                    'total_price_invoice_rental' => 298500,
                    'is_active_invoice_rental' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_invoice' => 1,
                    'id_client_invoice' => 3,
                    'notes_invoice_rental' => 'Penyewaan untuk ekspedisi',
                    'time_invoice_rental' => '13:00:00',
                    'total_weight_invoice_rental' => 400.0,
                    'price_invoice_rental' => 4500,
                    'promo_invoice_rental' => 15000,
                    'additional_cost_invoice_rental' => 10000,
                    'total_price_invoice_rental' => 1795000,
                    'is_active_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_invoice' => 2,
                    'id_client_invoice' => 6,
                    'notes_invoice_rental' => 'Diskon akhir tahun',
                    'time_invoice_rental' => '15:45:00',
                    'total_weight_invoice_rental' => 220.0,
                    'price_invoice_rental' => 3700,
                    'promo_invoice_rental' => 10000,
                    'additional_cost_invoice_rental' => 2000,
                    'total_price_invoice_rental' => 808000,
                    'is_active_invoice_rental' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_invoice' => 3,
                    'id_client_invoice' => 9,
                    'notes_invoice_rental' => 'Pengiriman luar kota',
                    'time_invoice_rental' => '17:20:00',
                    'total_weight_invoice_rental' => 180.0,
                    'price_invoice_rental' => 3900,
                    'promo_invoice_rental' => 5000,
                    'additional_cost_invoice_rental' => 5000,
                    'total_price_invoice_rental' => 702000,
                    'is_active_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_invoice' => 4,
                    'id_client_invoice' => 8,
                    'notes_invoice_rental' => 'Trial promo pengguna baru',
                    'time_invoice_rental' => '12:00:00',
                    'total_weight_invoice_rental' => 50.0,
                    'price_invoice_rental' => 5000,
                    'promo_invoice_rental' => 10000,
                    'additional_cost_invoice_rental' => 2000,
                    'total_price_invoice_rental' => 242000,
                    'is_active_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_branch_invoice' => 5,
                    'id_client_invoice' => 10,
                    'notes_invoice_rental' => 'Langganan tahunan',
                    'time_invoice_rental' => '18:10:00',
                    'total_weight_invoice_rental' => 350.0,
                    'price_invoice_rental' => 3600,
                    'promo_invoice_rental' => 20000,
                    'additional_cost_invoice_rental' => 15000,
                    'total_price_invoice_rental' => 1265000,
                    'is_active_invoice_rental' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            Log::info('Data invoice rental berhasil disimpan');
        } catch (Exception $error) {
            Log::error('Data invoice rental gagal disimpan', ['error' => $error->getMessage()]);
        }
    }
}