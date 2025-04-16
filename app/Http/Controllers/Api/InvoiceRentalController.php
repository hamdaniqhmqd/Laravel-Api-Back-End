<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\Invoice_Rental;
use App\Models\Logging;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InvoiceRentalController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        try {
            // Dapatkan semua data kecuali yang memiliki is_active_invoice_rental = 'inactive'
            $invoice_rental = Invoice_Rental::where('is_active_invoice_rental', '!=', 'inactive')->latest()->get();

            Log::info('Sukses menampilkan data invoice rental yang aktif');

            // Kembalikan data invoice rental sebagai resource
            return new ResponseApiResource(true, 'Daftar Data invoice rental Aktif', $invoice_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data invoice rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data invoice rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data invoice rental Tidak Ditemukan!', null, $error->getMessage(), 404);
        }
    }


    /**
     * getAll
     *
     * @return void
     */
    public function getAll()
    {
        try {
            // Mengambil semua data
            $invoice_rental =  Invoice_Rental::withTrashed()->latest()->get();

            Log::info('Sukses menampilkan data invoice rental');

            // Kembalikan data invoice rental sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data invoice rental', $invoice_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data invoice rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data invoice rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data invoice rental Tidak Ditemukan!', null, $error->getMessage(), 404);
        }
    }

    /**
     * getAllTrashed
     *
     * @return void
     */
    public function getAllTrashed()
    {
        try {
            // Mengambil semua data yang di hapus
            $invoice_rental =  Invoice_Rental::onlyTrashed()->where('is_active_invoice_rental', '!=', 'active')->latest()->get();

            Log::info('Sukses menampilkan data invoice rental yang dihapus');

            // Kembalikan data invoice rental sebagai resource
            return new ResponseApiResource(true, 'Daftar data invoice rental yang dihapus', $invoice_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data invoice rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data invoice rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data invoice rental Tidak Ditemukan!', null, $error->getMessage(), 404);
        }
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return void
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'id_branch_invoice' => 'required|exists:branches,id_branch',
                'id_client_invoice' => 'required|exists:clients,id_client',
                'notes_invoice_rental' => 'nullable|string',
                'total_weight_invoice_rental' => 'required|numeric|min:0',
                'price_invoice_rental' => 'required|numeric|min:0',
                'promo_invoice_rental' => 'nullable|numeric|min:0',
                'additional_cost_invoice_rental' => 'nullable|numeric|min:0',
                'is_active_invoice_rental' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors(), 422);
            }

            // Hitung total harga akhir invoice setelah promo dan biaya tambahan
            $promo = $request->promo_invoice_rental ?? 0;
            $additional = $request->additional_cost_invoice_rental ?? 0;

            $totalPrice = ($request->price_invoice_rental - $promo) + $additional;

            // Buat invoice rental
            $invoice = Invoice_Rental::create([
                'id_branch_invoice' => $request->id_branch_invoice,
                'id_client_invoice' => $request->id_client_invoice,
                'notes_invoice_rental' => $request->notes_invoice_rental,
                'time_invoice_rental' => Carbon::now()->toTimeString(),
                'total_weight_invoice_rental' => $request->total_weight_invoice_rental,
                'price_invoice_rental' => $request->price_invoice_rental,
                'promo_invoice_rental' => $promo,
                'additional_cost_invoice_rental' => $additional,
                'total_price_invoice_rental' => $totalPrice,
                'is_active_invoice_rental' => $request->is_active_invoice_rental,
            ]);

            Log::info('Invoice rental berhasil ditambahkan dengan ID: ' . $invoice->id_invoice_rental);

            return new ResponseApiResource(true, 'Invoice rental berhasil ditambahkan!', $invoice, null, 201);
        } catch (ValidationException $error) {
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('Error saat menambahkan invoice rental: ' . $e->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan invoice rental.', $request->all(), $e->getMessage(), 500);
        }
    }


    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        try {
            // Cari data berdasarkan ID
            $invoice_rental = Invoice_Rental::withTrashed()->find($id);

            // Periksa apakah Invoice Rental ditemukan
            if (!$invoice_rental) {
                Log::info('Invoice Rental tidak ditemukan dengan id ' . $id);

                return new ResponseApiResource(false, 'Invoice Rental tidak ditemukan', null, 404);
            }

            // Log info jika Invoice Rental ditemukan
            Log::info('Detail Invoice Rental ditemukan', ['id' => $invoice_rental->id_transaction_laundry, 'name_client_transaction_laundry' => $invoice_rental->name_client_transaction_laundry]);

            // Return data Invoice Rental sebagai resource
            return new ResponseApiResource(true, 'Detail Data Invoice Rental', $invoice_rental, null, 200);
        } catch (Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Gagal mengambil data Invoice Rental', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        try {
            // Cari invoice rental berdasarkan ID (termasuk yang soft deleted)
            $invoiceRental = Invoice_Rental::withTrashed()->find($id);
            if (!$invoiceRental) {
                Log::info('Invoice rental tidak ditemukan saat mencoba diubah', ['id_invoice_rental' => $id]);
                return new ResponseApiResource(false, 'Invoice rental tidak ditemukan.', [], null, 404);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'id_branch_invoice' => 'required|exists:branches,id_branch',
                'id_client_invoice' => 'required|exists:clients,id_client',
                'time_invoice_rental' => 'required|date_format:H:i:s',
                'notes_invoice_rental' => 'nullable|string',
                'total_weight_invoice_rental' => 'required|numeric|min:0',
                'price_invoice_rental' => 'required|numeric|min:0',
                'promo_invoice_rental' => 'nullable|numeric|min:0',
                'additional_cost_invoice_rental' => 'nullable|numeric|min:0',
                'total_price_invoice_rental' => 'required|numeric|min:0',
                'is_active_invoice_rental' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Data yang akan diupdate
            $data = [
                'id_branch_invoice' => $request->id_branch_invoice,
                'id_client_invoice' => $request->id_client_invoice,
                'time_invoice_rental' => $request->time_invoice_rental,
                'notes_invoice_rental' => $request->notes_invoice_rental,
                'total_weight_invoice_rental' => $request->total_weight_invoice_rental,
                'price_invoice_rental' => $request->price_invoice_rental,
                'promo_invoice_rental' => $request->promo_invoice_rental ?? 0,
                'additional_cost_invoice_rental' => $request->additional_cost_invoice_rental ?? 0,
                'total_price_invoice_rental' => $request->total_price_invoice_rental,
                'is_active_invoice_rental' => $request->is_active_invoice_rental,
            ];

            // Lakukan update
            $invoiceRental->update($data);

            // Jika nonaktif, soft delete
            if ($data['is_active_invoice_rental'] === 'inactive') {
                $invoiceRental->delete();
                Log::info('Invoice rental berhasil dinonaktifkan', [
                    'id_invoice_rental' => $id,
                    'id_client_invoice' => $invoiceRental->id_client_invoice
                ]);
            } else {
                $invoiceRental->restore();
                Log::info('Invoice rental berhasil diaktifkan kembali', [
                    'id_invoice_rental' => $id,
                    'id_client_invoice' => $invoiceRental->id_client_invoice
                ]);
            }

            return new ResponseApiResource(true, 'Invoice rental berhasil diperbarui.', $invoiceRental, null, 200);
        } catch (ValidationException $error) {
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('Error saat memperbarui invoice rental: ' . $e->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan saat memperbarui invoice rental.', $request->all(), $e->getMessage(), 500);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    /**
     * Menghapus Invoice Rental (soft delete)
     */
    public function destroy($id)
    {
        try {
            // Cari invoice rental berdasarkan ID
            $invoice_rental = Invoice_Rental::find($id);

            // Jika tidak ditemukan
            if (!$invoice_rental) {
                Log::info('Invoice Rental tidak ditemukan saat mencoba menghapus', ['id_invoice_rental' => $id]);

                return new ResponseApiResource(false, 'Invoice Rental tidak ditemukan!', [], $invoice_rental, 404);
            }

            // Ubah status menjadi inactive
            $invoice_rental->update(['is_active_invoice_rental' => 'inactive']);

            // Soft delete
            $invoice_rental->delete();

            Log::info('Invoice Rental berhasil dinonaktifkan', [
                'id_invoice_rental' => $id,
                'nomor_invoice' => $invoice_rental->nomor_invoice ?? null,
            ]);

            return new ResponseApiResource(true, 'Invoice Rental berhasil dinonaktifkan!', $invoice_rental, 200);
        } catch (\Exception $e) {
            Log::error('Gagal menonaktifkan Invoice Rental', [
                'id_invoice_rental' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }

    /**
     * Restore Invoice Rental yang sudah dihapus (soft delete)
     */
    public function restore($id)
    {
        try {
            $invoice_rental = Invoice_Rental::withTrashed()->find($id);

            if (!$invoice_rental) {
                Log::info('Invoice Rental tidak ditemukan saat mencoba dipulihkan', ['id_invoice_rental' => $id]);

                return new ResponseApiResource(false, 'Invoice Rental tidak ditemukan!', [], $invoice_rental, 404);
            }

            $invoice_rental->update(['is_active_invoice_rental' => 'active']);

            $invoice_rental->restore();

            Log::info('Invoice Rental berhasil dipulihkan', [
                'id_invoice_rental' => $id,
                'nomor_invoice' => $invoice_rental->nomor_invoice ?? null,
            ]);

            return new ResponseApiResource(true, 'Invoice Rental berhasil dipulihkan!', $invoice_rental, 200);
        } catch (\Exception $e) {
            Log::error('Gagal memulihkan Invoice Rental', [
                'id_invoice_rental' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }

    /**
     * Force delete (hapus permanen) Invoice Rental
     */
    public function forceDestroy($id)
    {
        try {
            $invoice_rental = Invoice_Rental::withTrashed()->find($id);

            if (!$invoice_rental) {
                Log::info('Invoice Rental tidak ditemukan saat mencoba hapus permanen', ['id_invoice_rental' => $id]);

                return new ResponseApiResource(false, 'Invoice Rental tidak ditemukan!', [], $invoice_rental, 404);
            }

            $invoice_rental->update(['is_active_invoice_rental' => 'inactive']);

            $invoice_rental->forceDelete();

            Log::info('Invoice Rental berhasil dihapus permanen', [
                'id_invoice_rental' => $id,
                'nomor_invoice' => $invoice_rental->nomor_invoice ?? null,
            ]);

            return new ResponseApiResource(true, 'Invoice Rental berhasil dihapus permanen!', $invoice_rental, 200);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus permanen Invoice Rental', [
                'id_invoice_rental' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }
}
