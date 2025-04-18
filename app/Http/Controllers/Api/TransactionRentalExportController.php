<?php

namespace App\Http\Controllers\Api;

use App\Exports\TransactionRentalExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\List_Transaction_Rental;
use App\Models\Transaction_Rental;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class TransactionRentalExportController extends Controller
{
    //
    public function export($id)
    {
        try {
            // Ambil data transaksi utama
            $transactionRental = Transaction_Rental::findOrFail($id);

            // Ambil data list transaksi terkait
            $listTransactionRentals = List_Transaction_Rental::where('id_rental_transaction', $id)->get();

            // Tanggal sekarang
            $monthPath = now()->format('Y-m'); // contoh: 2025-04
            $day = now()->format('Y-m-d');     // contoh: 2025-04-17

            // Path folder penyimpanan
            $folderPath = 'transaction_rental/' . $monthPath . '/' . $day;

            // Cek dan buat folder jika belum ada
            $storagePath = storage_path('app/public/' . $folderPath);
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            // Ambil semua file yang sudah ada dan cari nomor terakhir
            $existingFiles = collect(scandir($storagePath))
                ->filter(fn($file) => preg_match('/^(\d+)\.xlsx$/', $file))
                ->map(fn($file) => (int) basename($file, '.xlsx'))
                ->sort()
                ->values();

            $nextNumber = $existingFiles->isNotEmpty() ? $existingFiles->last() + 1 : 1;

            // Buat nama file dan path
            $filename = $nextNumber . '.xlsx';
            $path = $folderPath . '/' . $filename;

            // Simpan file ke storage/public
            Excel::store(
                new TransactionRentalExport($transactionRental, $listTransactionRentals),
                $path,
                'public'
            );

            // Buat URL untuk download
            $url = asset('storage/' . $path);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Berhasil generate file Excel', [
                'download_url' => $url,
                'filename' => $filename,
                'path' => $path
            ], null, 200);
        } catch (ModelNotFoundException $e) {
            Log::error("Error (Transaski tidak ditemukan): " . $e->getMessage());
            return new ResponseApiResource(false, 'Transaksi tidak ditemukan.', null, $e->getMessage(), 404);
        } catch (Exception $e) {
            Log::error("Error (Gagal generate file Excel): " . $e->getMessage());
            return new ResponseApiResource(false, 'Gagal generate file Excel.', null, $e->getMessage(), 500);
        }
    }
}