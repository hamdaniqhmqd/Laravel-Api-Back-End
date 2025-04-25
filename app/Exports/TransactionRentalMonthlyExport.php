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
    protected $branchId;
    protected $rentalType;
    protected $periodName;
    protected $initialStock;
    protected $notes;

    public function __construct($month, $branchId, $rentalType, $periodName, $initialStock, $notes = [])
    {
        $this->month = $month;
        $this->branchId = $branchId;
        $this->rentalType = $rentalType;
        $this->periodName = $periodName;
        $this->initialStock = $initialStock;
        $this->notes = $notes;
    }

    public function view(): View
    {
        // Start and end date for the selected month
        $startDate = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $this->month)->endOfMonth();

        // Get month and year numbers
        $month = $startDate->month;
        $year = $startDate->year;

        // Fetch branch name from database
        $branch = DB::table('branches')
            ->select('name_branch as branch_name')
            ->where('id_branch', $this->branchId)
            ->first();

        $branchName = $branch ? $branch->branch_name : 'Unknown Branch';

        // Modified query to join with users table to get courier name
        $transactions = DB::table('transaction_rentals')
            ->leftJoin('users', 'transaction_rentals.id_kurir_transaction_rental', '=', 'users.id_user')
            ->select(
                DB::raw('DATE(transaction_rentals.created_at) as tanggal'),
                DB::raw('TIME(transaction_rentals.created_at) as jam'),
                'fullname_user as kurir_name', // Get courier's full name
                'id_kurir_transaction_rental as kurir_id',
                'recipient_name_transaction_rental as penerima',
                'total_pcs_transaction_rental as qty',
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
                '),
                DB::raw('
                    CASE 
                        WHEN status_transaction_rental IN ("in", "approved", "waiting for approval") THEN total_weight_transaction_rental 
                        ELSE 0 
                    END as berat_masuk
                '),
                DB::raw('
                    CASE 
                        WHEN status_transaction_rental = "out" THEN total_weight_transaction_rental 
                        ELSE 0 
                    END as berat_keluar
                ')
            )
            ->where('id_branch_transaction_rental', $this->branchId)
            ->where('type_rental_transaction', $this->rentalType)
            ->whereRaw('MONTH(transaction_rentals.created_at) = ? AND YEAR(transaction_rentals.created_at) = ?', [$month, $year])
            ->where(function ($query) {
                // Handle various is_active formats
                $query->where('is_active_transaction_rental', 'active');
            })
            ->orderBy('transaction_rentals.created_at')
            ->get();

        // Log for debugging
        Log::info('Retrieved ' . count($transactions) . ' transactions for month ' . $this->month . ', branch ID ' . $this->branchId . ', type ' . $this->rentalType);

        // Calculate running stock totals
        $currentStock = $this->initialStock;
        $totalInWeight = 0;
        $totalOutWeight = 0;

        foreach ($transactions as $transaction) {
            // Handle stock in (masuk)
            if ($transaction->masuk > 0) {
                $currentStock += $transaction->masuk;
                $transaction->stok_after_masuk = $currentStock;
                $totalInWeight += $transaction->berat_masuk;
            }

            // Handle stock out (keluar)
            if ($transaction->keluar > 0) {
                $currentStock -= $transaction->keluar;
                $transaction->stok_after_keluar = $currentStock;
                $totalOutWeight += $transaction->berat_keluar;
            }
        }

        return view('export.transaction_rental_monthly', [
            'transactions' => $transactions,
            'totalInWeight' => $totalInWeight,
            'totalOutWeight' => $totalOutWeight,
            'location' => $branchName,
            'description' => $this->rentalType,
            'period' => $this->periodName,
            'title' => $this->rentalType,
            'initialStock' => $this->initialStock,
            'notes' => $this->notes
        ]);
    }

    public function title(): string
    {
        return 'Report ' . $this->rentalType;
    }
}
