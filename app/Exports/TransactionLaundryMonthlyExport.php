<?php

namespace App\Exports;

use App\Models\Transaction_Laundry;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransactionLaundryMonthlyExport implements FromView, WithTitle
{
    protected $month;
    protected $branchId;
    protected $periodName;
    protected $notes;

    public function __construct($month, $branchId, $periodName, $notes = [])
    {
        $this->month = $month;
        $this->branchId = $branchId;
        $this->periodName = $periodName;
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

        // Query to fetch laundry transactions with user (courier) information
        $transactions = DB::table('transaction_laundries')
            ->leftJoin('users', 'transaction_laundries.id_user_transaction_laundry', '=', 'users.id_user')
            ->select(
                'transaction_laundries.id_transaction_laundry',
                'transaction_laundries.name_client_transaction_laundry as client_name',
                'fullname_user as staff_name',
                'id_user_transaction_laundry as user_id',
                'status_transaction_laundry as status',
                'notes_transaction_laundry as notes',
                'total_weight_transaction_laundry as weight',
                'total_price_transaction_laundry as price',
                'count_item_laundry_transaction_laundry as qty',
                'promo_transaction_laundry as promo',
                'additional_cost_transaction_laundry as additional_cost',
                'total_transaction_laundry as total',
                'cash_transaction_laundry as cash',
                'change_money_transaction_laundry as change',
                DB::raw('DATE(transaction_laundries.created_at) as tanggal'),
                DB::raw('TIME(transaction_laundries.created_at) as jam'),
                'first_date_transaction_laundry as first_date',
                'last_date_transaction_laundry as last_date'
            )
            ->where('id_branch_transaction_laundry', $this->branchId)
            ->whereRaw('MONTH(transaction_laundries.created_at) = ? AND YEAR(transaction_laundries.created_at) = ?', [$month, $year])
            ->where(function ($query) {
                // Handle various is_active formats
                $query->where('is_active_transaction_laundry', 'active')
                    ->orWhere('is_active_transaction_laundry', 1)
                    ->orWhere('is_active_transaction_laundry', '1');
            })
            ->orderBy('transaction_laundries.created_at')
            ->get();

        // Log for debugging
        Log::info('Retrieved ' . count($transactions) . ' laundry transactions for month ' . $this->month . ', branch ID ' . $this->branchId);

        // Calculate totals
        $totalWeight = 0;
        $totalPrice = 0;
        $totalQty = 0;
        $totalPromo = 0;
        $totalAdditionalCost = 0;
        $totalAmount = 0;

        foreach ($transactions as $transaction) {
            $totalWeight += $transaction->weight;
            $totalPrice += $transaction->price;
            $totalQty += $transaction->qty;
            $totalPromo += $transaction->promo;
            $totalAdditionalCost += $transaction->additional_cost;
            $totalAmount += $transaction->total;
        }

        return view('export.transaction_laundry_monthly', [
            'transactions' => $transactions,
            'totalWeight' => $totalWeight,
            'totalPrice' => $totalPrice,
            'totalQty' => $totalQty,
            'totalPromo' => $totalPromo,
            'totalAdditionalCost' => $totalAdditionalCost,
            'totalAmount' => $totalAmount,
            'location' => $branchName,
            'period' => $this->periodName,
            'notes' => $this->notes
        ]);
    }

    public function title(): string
    {
        return 'Report Laundry ' . $this->month;
    }
}
