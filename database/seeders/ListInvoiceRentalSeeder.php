<?php

namespace Database\Seeders;

use App\Models\List_Invoice_Rental;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ListInvoiceRentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            List_Invoice_Rental::insert([
                [
                    'id_rental_invoice' => 1,
                    'id_rental_transaction' => 1,
                    'status_list_invoice_rental' => 'unpaid',
                    'note_list_invoice_rental' => 'Menunggu pembayaran dari klien',
                    'price_list_invoice_rental' => 175000.00,
                    'weight_list_invoice_rental' => 80.50,
                    'is_active_list_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_rental_invoice' => 1,
                    'id_rental_transaction' => 1,
                    'status_list_invoice_rental' => 'paid',
                    'note_list_invoice_rental' => 'Pembayaran telah diterima',
                    'price_list_invoice_rental' => 202000.00,
                    'weight_list_invoice_rental' => 100.00,
                    'is_active_list_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_rental_invoice' => 2,
                    'id_rental_transaction' => 1,
                    'status_list_invoice_rental' => 'cancelled',
                    'note_list_invoice_rental' => 'Transaksi dibatalkan karena kesalahan input',
                    'price_list_invoice_rental' => 0.00,
                    'weight_list_invoice_rental' => 0.00,
                    'is_active_list_invoice_rental' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_rental_invoice' => 3,
                    'id_rental_transaction' => 1,
                    'status_list_invoice_rental' => 'paid',
                    'note_list_invoice_rental' => null,
                    'price_list_invoice_rental' => 350000.00,
                    'weight_list_invoice_rental' => 150.75,
                    'is_active_list_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_rental_invoice' => 4,
                    'id_rental_transaction' => 1,
                    'status_list_invoice_rental' => 'unpaid',
                    'note_list_invoice_rental' => 'Klien minta waktu tambahan',
                    'price_list_invoice_rental' => 289000.00,
                    'weight_list_invoice_rental' => 130.00,
                    'is_active_list_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_rental_invoice' => 5,
                    'id_rental_transaction' => 1,
                    'status_list_invoice_rental' => 'paid',
                    'note_list_invoice_rental' => 'Pembayaran dilakukan via transfer',
                    'price_list_invoice_rental' => 120000.00,
                    'weight_list_invoice_rental' => 90.00,
                    'is_active_list_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_rental_invoice' => 6,
                    'id_rental_transaction' => 1,
                    'status_list_invoice_rental' => 'unpaid',
                    'note_list_invoice_rental' => 'Invoice baru dibuat',
                    'price_list_invoice_rental' => 400000.00,
                    'weight_list_invoice_rental' => 200.00,
                    'is_active_list_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_rental_invoice' => 7,
                    'id_rental_transaction' => 1,
                    'status_list_invoice_rental' => 'paid',
                    'note_list_invoice_rental' => null,
                    'price_list_invoice_rental' => 245000.00,
                    'weight_list_invoice_rental' => 135.00,
                    'is_active_list_invoice_rental' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_rental_invoice' => 8,
                    'id_rental_transaction' => 1,
                    'status_list_invoice_rental' => 'cancelled',
                    'note_list_invoice_rental' => 'Pelanggan membatalkan karena barang rusak',
                    'price_list_invoice_rental' => 0.00,
                    'weight_list_invoice_rental' => 0.00,
                    'is_active_list_invoice_rental' => 'inactive',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
                [
                    'id_rental_invoice' => 9,
                    'id_rental_transaction' => 1,
                    'status_list_invoice_rental' => 'paid',
                    'note_list_invoice_rental' => 'Lunas dan dikirim',
                    'price_list_invoice_rental' => 312000.00,
                    'weight_list_invoice_rental' => 120.00,
                    'is_active_list_invoice_rental' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ],
            ]);

            Log::info('Data list invoice rental berhasil disimpan');
        } catch (\Exception $error) {
            Log::error('Data list invoice rental gagal disimpan', ['error' => $error->getMessage()]);
        }
    }
}
