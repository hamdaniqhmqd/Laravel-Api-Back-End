<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ResponseApiResource;
use App\Exports\TransactionLaundryMonthlyExport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Exception;

class TransactionLaundryExportController extends Controller
{
    /**
     * Export monthly laundry transaction data via API
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
                'notes' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return new ResponseApiResource(false, 'Validasi gagal', null, $validator->errors(), 422);
            }

            $month = $request->month;
            $location = $request->location;
            $notes = $request->notes ?? [];

            // Generate month name for title
            $monthDate = Carbon::createFromFormat('Y-m', $month);
            $periodName = $monthDate->translatedFormat('F Y');

            // Create folder structure
            $folderPath = 'transaction_laundry/' . $month;

            // Check and create directory if not exists
            $storagePath = storage_path('app/public/' . $folderPath);
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            // Base filename without extension
            $baseFilename = 'Report_Laundry_' . $month;

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
                new TransactionLaundryMonthlyExport($month, $location, $periodName, $notes),
                $path,
                'public'
            );

            Log::info('Monthly laundry report file saved at: ' . storage_path('app/public/' . $path));

            // Return download URL
            $url = asset('storage/' . $path);

            return new ResponseApiResource(true, 'Berhasil generate file Excel', [
                'download_url' => $url,
                'filename' => $filename,
                'path' => $path
            ], null, 200);
        } catch (Exception $e) {
            Log::error("Error generating monthly laundry report: " . $e->getMessage());
            return new ResponseApiResource(false, 'Gagal generate report.', null, $e->getMessage(), 500);
        }
    }
}
