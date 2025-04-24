<?php

namespace App\Http\Controllers\Api;

use App\Exports\TransactionRentalExport;
use App\Exports\TransactionRentalMonthlyExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\List_Transaction_Rental;
use App\Models\Transaction_Rental;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class TransactionRentalExportController extends Controller
{
    /**
     * Export monthly transaction data via API
     * 
     * @param Request $request
     * @return ResponseApiResource
     */
    public function exportMonthly(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'month' => 'required|date_format:Y-m',
                'location' => 'required|exists:branches,id_branch',
                'description' => 'required|in:bath towel,hand towel,gorden,keset',
                'notes' => 'nullable|array',
                'initial_stock' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return new ResponseApiResource(false, 'Validasi gagal', null, $validator->errors(), 422);
            }

            $month = $request->month;
            $location = $request->location;
            $description = $request->description;
            $notes = $request->notes ?? [];
            $initialStock = $request->initial_stock; // Get initial stock value

            // Generate month name for title
            $monthDate = Carbon::createFromFormat('Y-m', $month);
            $periodName = $monthDate->translatedFormat('F Y');

            // Create folder structure
            $folderPath = 'transaction_rental/' . $month;

            // Check and create directory if not exists
            $storagePath = storage_path('app/public/' . $folderPath);
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            // Base filename without extension
            $baseFilename = 'Report_' . str_replace(' ', '_', $description) . '_' . $month;

            // Check for existing files with similar names and get the next increment number
            $existingFiles = glob($storagePath . '/' . $baseFilename . '*.xlsx');
            $highestIncrement = 0;

            foreach ($existingFiles as $file) {
                // Extract increment number from existing files
                if (preg_match('/' . preg_quote($baseFilename, '/') . '_(\d+)\.xlsx$/', $file, $matches)) {
                    $increment = (int)$matches[1];
                    $highestIncrement = max($highestIncrement, $increment);
                }
            }

            // Generate new filename with increment
            $increment = $highestIncrement + 1;
            $filename = $baseFilename . '_' . $increment . '.xlsx';
            $path = $folderPath . '/' . $filename;

            // Store Excel file
            Excel::store(
                new TransactionRentalMonthlyExport($month, $location, $description, $periodName, $initialStock, $notes),
                $path,
                'public'
            );

            Log::info('Monthly report file saved at: ' . storage_path('app/public/' . $path));

            // Return download URL
            $url = asset('storage/' . $path);

            return new ResponseApiResource(true, 'Berhasil generate file Excel', [
                'download_url' => $url,
                'filename' => $filename,
                'path' => $path
            ], null, 200);
        } catch (Exception $e) {
            Log::error("Error generating monthly report: " . $e->getMessage());
            return new ResponseApiResource(false, 'Gagal generate report.', null, $e->getMessage(), 500);
        }
    }
}
