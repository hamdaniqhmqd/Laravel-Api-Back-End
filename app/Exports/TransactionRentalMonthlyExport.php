<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Models\Transaction_Rental;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;

class TransactionRentalMonthlyExport implements FromView, WithTitle
{
    protected $month;
    protected $location;
    protected $description;
    protected $periodName;
    protected $initialStock;
    protected $notes;

    public function __construct($month, $location, $description, $periodName, $initialStock, $notes = [])
    {
        $this->month = $month;
        $this->location = $location;
        $this->description = $description;
        $this->periodName = $periodName;
        $this->initialStock = $initialStock;
        $this->notes = $notes;
    }

    public function view(): View
    {
        // Start and end date for the selected month
        $startDate = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $this->month)->endOfMonth();

        // Ambil bulan dan tahun sebagai angka
        $month = $startDate->month;
        $year = $startDate->year;

        // Query yang lebih fleksibel dengan kondisi OR untuk menangkap semua transaksi pada bulan tersebut
        $transactions = DB::table('transaction_rentals')
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('TIME(created_at) as jam'),
                'id_kurir_transaction_rental as kurir',
                'recipient_name_transaction_rental as penerima',
                'total_pcs_transaction_rental as qty',
                'total_weight_transaction_rental as berat',
                'status_transaction_rental as status',
                DB::raw('
                CASE 
                    WHEN status_transaction_rental IN ("in", "approved", "waiting for approval") THEN total_pcs_transaction_rental 
                    ELSE 0 
                END as masuk
            '),
                DB::raw('
                CASE 
                    WHEN status_transaction_rental = "out" THEN total_pcs_transaction_rental 
                    ELSE 0 
                END as keluar
            ')
            )
            ->where(function ($query) use ($month, $year) {
                // Cari di first_date ATAU created_at yang berada pada bulan tersebut
                $query->whereRaw('MONTH(created_at) = ? AND YEAR(created_at) = ?', [$month, $year])
                    ->orWhereRaw('MONTH(created_at) = ? AND YEAR(created_at) = ?', [$month, $year]);
            })
            ->where(function ($query) {
                // Handle berbagai kemungkinan format is_active
                $query->where('is_active_transaction_rental', 'active')
                    ->orWhere('is_active_transaction_rental', 1)
                    ->orWhere('is_active_transaction_rental', '1');
            })
            ->orderBy('created_at')
            ->get();

        // Debugging - tambahkan log untuk melihat berapa banyak data yang diperoleh
        Log::info('Retrieved ' . count($transactions) . ' transactions for month ' . $this->month);

        // Calculate running stock totals, starting from the initial stock value provided by the user
        $currentStock = $this->initialStock; // Use the manually inputted initial stock
        $totalWeight = 0;

        foreach ($transactions as $key => $transaction) {
            if ($transaction->masuk > 0) {
                $currentStock += $transaction->masuk;
                $transaction->stok_after_masuk = $currentStock;
                $totalWeight += $transaction->berat;
            }

            if ($transaction->keluar > 0) {
                $currentStock -= $transaction->keluar;
                $transaction->stok_after_keluar = $currentStock;
                $totalWeight += $transaction->berat;
            }
        }

        return view('export.transaction_rental_monthly', [
            'transactions' => $transactions,
            'totalWeight' => $totalWeight,
            'location' => $this->location,
            'description' => $this->description,
            'period' => $this->periodName,
            'title' => $this->description,
            'initialStock' => $this->initialStock,
            'notes' => $this->notes
        ]);
    }

    public function title(): string
    {
        return 'Report ' . $this->description;
    }
}
