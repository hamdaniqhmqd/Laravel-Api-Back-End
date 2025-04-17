<?php

namespace App\Exports;

use App\Models\List_Transaction_Rental;
use App\Models\Transaction_Rental;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class TransactionRentalExport implements FromArray, WithTitle
{
    protected $transactionRental;
    protected $listTransactionRentals;

    public function __construct($transactionRental, $listTransactionRentals)
    {
        $this->transactionRental = $transactionRental;
        $this->listTransactionRentals = $listTransactionRentals;
    }

    public function array(): array
    {
        $headerTransaksi = [
            ['ID Transaksi Rental', $this->transactionRental->id_transaction_rental],
            ['Nama Penerima', $this->transactionRental->recipient_name_transaction_rental],
            ['Status', $this->transactionRental->status_transaction_rental],
            ['Total Berat', $this->transactionRental->total_weight_transaction_rental],
            ['Total Pcs', $this->transactionRental->total_pcs_transaction_rental],
            ['Harga Promo', $this->transactionRental->promo_transaction_rental],
            ['Biaya Tambahan', $this->transactionRental->additional_cost_transaction_rental],
            ['Total Harga', $this->transactionRental->total_price_transaction_rental],
            ['Catatan', $this->transactionRental->notes_transaction_rental],
            ['Tanggal Pertama', $this->transactionRental->first_date_transaction_rental],
            ['Tanggal Terakhir', $this->transactionRental->last_date_transaction_rental],
            [], // baris kosong
            ['Detail List Transaksi Rental'], // judul list
            [
                'ID List Transaksi Rental',
                'ID Barang Rental',
                'Status',
                'Kondisi',
                'Catatan',
                'Harga',
                'Berat',
                'Status Aktif'
            ],
        ];

        $dataList = $this->listTransactionRentals->map(function ($item) {
            return [
                $item->id_list_transaction_rental,
                $item->id_item_rental,
                $item->status_list_transaction_rental,
                $item->condition_list_transaction_rental,
                $item->note_list_transaction_rental,
                $item->price_list_transaction_rental,
                $item->weight_list_transaction_rental,
                $item->is_active_list_transaction_rental,
            ];
        })->toArray();

        return array_merge($headerTransaksi, $dataList);
    }

    public function title(): string
    {
        return 'Transaksi Rental';
    }
}
