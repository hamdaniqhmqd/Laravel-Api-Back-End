<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\Invoice_Rental;
use Illuminate\Support\Str;
use App\Models\List_Invoice_Rental;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceRentalExportController extends Controller
{
    /**
     * Generate and download PDF invoice
     * 
     * @param Request $request
     * @return ResponseApiResource
     */
    public function generateInvoicePdf(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'id_invoice_rental' => 'required|integer|exists:invoice_rentals,id_invoice_rental',
                'note' => 'nullable|string|max:255',
                'payment' => 'required|string|regex:/^[0-9\-]+$/',  // Perbaikan validasi payment
            ]);

            $invoiceId = $request->id_invoice_rental;
            $customNote = $request->note; // ambil note dari request
            $payment = $request->payment; // ambil payment dari request

            // Get invoice data with relations
            $invoice = Invoice_Rental::with(['branch', 'client'])->findOrFail($invoiceId);

            // Get list items and group by type
            $listItems = List_Invoice_Rental::where('id_rental_invoice', $invoiceId)
                ->where('is_active_list_invoice_rental', 1)
                ->get();

            // Group and sum by type
            $summarizedByType = $listItems->groupBy('type_invoice_rental')
                ->map(function ($group) {
                    return [
                        'type' => $group->first()->type_invoice_rental,
                        'total_weight' => $group->sum('weight_list_invoice_rental'),
                        'total_price' => $group->sum('total_price_invoice_rental'),
                        'price_weight' => $group->first()->price_list_invoice_rental,
                        'items' => $group
                    ];
                })->values()->toArray();

            // Format the data for the PDF view
            $data = [
                'invoice' => [
                    'id' => $invoice->id_invoice_rental,
                    'number' => $invoice->number_invoice,
                    'date' => Carbon::parse($invoice->created_at)->format('d F Y'),
                    'due_date' => Carbon::parse($invoice->created_at)->addDays(14)->format('d F Y'),
                    'notes' => $invoice->notes_invoice_rental,
                    'custom_note' => $customNote, // NOTE khusus dari input
                    'total_weight' => $invoice->total_weight_invoice_rental,
                    'price' => $invoice->price_invoice_rental,
                    'promo' => $invoice->promo_invoice_rental,
                    'additional_cost' => $invoice->additional_cost_invoice_rental,
                    'total_price' => $invoice->total_price_invoice_rental,
                    'payment' => $payment, // Payment yang diinputkan
                ],
                'client' => $invoice->client ? [
                    'name' => $invoice->client->name_client ?? '-',
                    'address' => $invoice->client->address_client ?? '-',
                    'phone' => $invoice->client->phone_client ?? '-',
                ] : null,
                'branch' => $invoice->branch ? [
                    'name' => $invoice->branch->name_branch ?? '-',
                    'address' => $invoice->branch->address_branch ?? '-',
                    'phone' => $invoice->branch->phone_branch ?? '-',
                ] : null,
                'items_by_type' => $summarizedByType,
                'items' => $listItems,
            ];

            // Set PDF filename and path
            $monthFolder = now()->format('Y-m');
            $folderPath = 'invoice_rental/' . $monthFolder;
            $storagePath = storage_path('app/public/' . $folderPath);

            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $baseFilename = 'Invoice_' . Str::slug($invoice->number_invoice);
            $existingFiles = glob($storagePath . '/' . $baseFilename . '*.pdf');
            $highestIncrement = 0;

            foreach ($existingFiles as $file) {
                if (preg_match('/' . preg_quote($baseFilename, '/') . '_(\d+)\.pdf$/', $file, $matches)) {
                    $increment = (int)$matches[1];
                    $highestIncrement = max($highestIncrement, $increment);
                }
            }

            $increment = $highestIncrement + 1;
            $filename = $baseFilename . '_' . $increment . '.pdf';
            $path = $folderPath . '/' . $filename;

            // Simpan PDF
            $pdf = Pdf::loadView('pdf.invoice_rental', compact('data'));
            // $pdf->getDomPDF()->setOptions(['isRemoteEnabled' => true]);
            $pdf->save(storage_path('app/public/' . $path));

            // URL untuk download
            $url = asset('storage/' . $path);

            // Return response JSON sesuai format
            return new ResponseApiResource(true, 'Berhasil generate file PDF', [
                'download_url' => $url,
                'filename' => $filename,
                'path' => $path
            ], null, 200);
        } catch (\Exception $e) {
            return new ResponseApiResource(false, 'Failed to generate invoice PDF', null, $e->getMessage(), 500);
        }
    }

    public function testInvoiceView()
    {
        // Data dummy untuk testing
        $data = [
            'branch' => [
                'name' => 'Ubud',
                'address' => 'Jl. Raya Mas No. 60',
                'phone' => '081234567890'
            ],
            'invoice' => [
                'number' => '001/INV/BL-P/01/25',
                'date' => '2025-02-01',
                'price' => 6800,
                'total_price' => 2421548,
                'promo' => 0,
                'additional_cost' => 0,
                'notes' => 'Apap niiii',
                'custom_note' => 'Apap niiii',
                'payment' => '31213-312312-123123-3123',
            ],
            'client' => [
                'name' => 'Fitness Plus Ubud',
                'address' => 'Jl. Raya Mas No. 60, MAS',
                'district' => 'Kecamatan Ubud, Kabupaten Gianyar,',
                'city_postal' => 'Bali, 80571',
                'phone' => ''
            ],
            'items_by_type' => [
                [
                    'type' => 'Bath Towel',
                    'total_weight' => 149.63,
                    'total_price' => 1017484,
                    'price_weight' => 1017484
                ],
                [
                    'type' => 'Hand Towel',
                    'total_weight' => 206.48,
                    'total_price' => 1404064,
                    'price_weight' => 1017484
                ]
            ]
        ];

        // Untuk melihat tampilan dalam browser (debugging)
        return view('pdf.invoice_rental', compact('data'));
    }
}
