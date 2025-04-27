<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\List_Invoice_Rental;
use App\Models\Logging;
use App\Models\Transaction_Rental;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ListInvoiceRentalController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        try {
            // Dapatkan semua data kecuali yang memiliki is_active_list_invoice_rental = 'inactive'
            $list_invoice_rental = List_Invoice_Rental::where('is_active_list_invoice_rental', '!=', 'inactive')->latest()->get();

            Log::info('Sukses menampilkan data list invoice rental yang aktif');

            // Kembalikan data list invoice rental sebagai resource
            return new ResponseApiResource(true, 'Daftar Data list invoice rental Aktif', $list_invoice_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data list invoice rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data list invoice rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data list invoice rental Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            $list_invoice_rental =  List_Invoice_Rental::withTrashed()->latest()->get();

            Log::info('Sukses menampilkan data list invoice rental');

            // Kembalikan data list invoice rental sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data list invoice rental', $list_invoice_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data list invoice rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data list invoice rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data list invoice rental Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            $list_invoice_rental =  List_Invoice_Rental::onlyTrashed()->where('is_active_list_invoice_rental', '!=', 'active')->latest()->get();

            Log::info('Sukses menampilkan data list invoice rental yang dihapus');

            // Kembalikan data list invoice rental sebagai resource
            return new ResponseApiResource(true, 'Daftar data list invoice rental yang dihapus', $list_invoice_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data list invoice rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data list invoice rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data list invoice rental Tidak Ditemukan!', null, $error->getMessage(), 404);
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
                'id_rental_invoice' => 'required|exists:invoice_rentals,id_invoice_rental',
                'id_rental_transaction' => 'required|exists:transaction_rentals,id_transaction_rental',
                'status_list_invoice_rental' => 'nullable|in:unpaid,paid,cancelled',
                'type_invoice_rental' => 'required|in:bath towel,hand towel,gorden,keset',
                'note_list_invoice_rental' => 'nullable|string',
                'is_active_list_invoice_rental' => 'nullable|in:active,inactive',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors(), 422);
            }

            // Ambil data transaction rental
            $rentalTransaction = Transaction_Rental::findOrFail($request->id_rental_transaction);

            // Periksa apakah transaksi rental ditemukan
            if (!$rentalTransaction) {
                Log::info('Data transaksi rental tidak ditemukan', ['id_rental_transaction' => $request->id_rental_transaction]);
                return new ResponseApiResource(false, 'Data transaksi rental tidak ditemukan', null, 404);
            }

            // Periksa apakah type invoice rental valid
            if (strtolower($request->type_invoice_rental) !== strtolower($rentalTransaction->type_rental_transaction)) {
                Log::warning('Tipe transaksi tidak sesuai: ' . $request->type_invoice_rental);

                return new ResponseApiResource(
                    false,
                    'Tipe transaksi tidak sesuai',
                    $request->all(),
                    'Tipe transaksi tidak sesuai: ' . $request->type_invoice_rental . ', Yang harusnya ' . $rentalTransaction->type_rental_transaction,
                    422
                );
            }

            // Hitung total price berdasarkan price_weight dan total_weight
            $priceWeightListInvoiceRental = $rentalTransaction->price_weight_transaction_rental;
            $totalWeight = $rentalTransaction->total_weight_transaction_rental;
            $totalPriceListInvoice = $priceWeightListInvoiceRental * $totalWeight;

            // Buat data list invoice rental
            $listInvoice = List_Invoice_Rental::create([
                'id_rental_invoice' => $request->id_rental_invoice,
                'id_rental_transaction' => $request->id_rental_transaction,
                'type_invoice_rental' => $rentalTransaction->type_rental_transaction,
                'status_list_invoice_rental' => $request->status_list_invoice_rental ?? 'unpaid',
                'note_list_invoice_rental' => $request->note_list_invoice_rental,
                'price_list_invoice_rental' => $priceWeightListInvoiceRental,
                'weight_list_invoice_rental' => $totalWeight,
                'total_price_invoice_rental' => $totalPriceListInvoice,
                'is_active_list_invoice_rental' => $request->is_active_list_invoice_rental ?? 'active',
            ]);

            Log::info('List invoice rental berhasil ditambahkan dengan ID: ' . $listInvoice->id_list_invoice_rental);

            return new ResponseApiResource(true, 'List invoice rental berhasil ditambahkan!', $listInvoice, null, 201);
        } catch (ValidationException $error) {
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('Error saat menambahkan list invoice rental: ' . $e->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan list invoice rental.', $request->all(), $e->getMessage(), 500);
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
            $list_invoice_rental = List_Invoice_Rental::withTrashed()->find($id);

            // Periksa apakah List Invoice Rental ditemukan
            if (!$list_invoice_rental) {
                Log::info('List Invoice Rental tidak ditemukan dengan id ' . $id);

                return new ResponseApiResource(false, 'List Invoice Rental tidak ditemukan', null, 404);
            }

            // Log info jika List Invoice Rental ditemukan
            Log::info('Detail List Invoice Rental ditemukan', ['id' => $list_invoice_rental->id_transaction_laundry, 'name_client_transaction_laundry' => $list_invoice_rental->name_client_transaction_laundry]);

            // Return data List Invoice Rental sebagai resource
            return new ResponseApiResource(true, 'Detail Data List Invoice Rental', $list_invoice_rental, null, 200);
        } catch (Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Gagal mengambil data List Invoice Rental', [
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
            // Cari List Invoice Rental berdasarkan ID (termasuk yang soft deleted)
            $invoiceRental = List_Invoice_Rental::withTrashed()->find($id);
            if (!$invoiceRental) {
                Log::info('List Invoice Rental tidak ditemukan saat mencoba diubah', ['id_invoice_rental' => $id]);
                return new ResponseApiResource(false, 'List Invoice Rental tidak ditemukan.', [], null, 404);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'id_rental_invoice' => 'required|exists:invoice_rentals,id_invoice_rental',
                'id_rental_transaction' => 'required|exists:transaction_rentals,id_transaction_rental',
                'status_list_invoice_rental' => 'required|in:unpaid,paid,cancelled',
                'note_list_invoice_rental' => 'nullable|string',
                'is_active_list_invoice_rental' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors(), 422);
            }

            // Ambil data Transaction_Rental terkait
            $rentalTransaction = Transaction_Rental::findOrFail($request->id_rental_transaction);

            $priceWeightListInvoiceRental = $rentalTransaction->price_weight_transaction_rental;
            $totalWeight = $rentalTransaction->total_weight_transaction_rental;
            $totalPriceListInvoice = $priceWeightListInvoiceRental * $totalWeight;

            // Siapkan data untuk diupdate
            $data = [
                'id_rental_invoice' => $request->id_rental_invoice,
                'id_rental_transaction' => $request->id_rental_transaction,
                'type_invoice_rental' => $rentalTransaction->type_rental_transaction,
                'status_list_invoice_rental' => $request->status_list_invoice_rental,
                'note_list_invoice_rental' => $request->note_list_invoice_rental ?? null,
                'price_list_invoice_rental' => $priceWeightListInvoiceRental,
                'weight_list_invoice_rental' => $totalWeight,
                'total_price_invoice_rental' => $totalPriceListInvoice,
                'is_active_list_invoice_rental' => $request->is_active_list_invoice_rental,
            ];

            // Update data
            $invoiceRental->update($data);

            // Handle soft delete jika nonaktif
            if ($data['is_active_list_invoice_rental'] === 'inactive') {
                $invoiceRental->delete();
                Log::info('List Invoice Rental berhasil dinonaktifkan', [
                    'id_list_invoice_rental' => $id
                ]);
            } else {
                $invoiceRental->restore();
                Log::info('List Invoice Rental berhasil diaktifkan kembali', [
                    'id_list_invoice_rental' => $id
                ]);
            }

            return new ResponseApiResource(true, 'List Invoice Rental berhasil diperbarui.', $invoiceRental, null, 200);
        } catch (ValidationException $error) {
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('Error saat memperbarui List Invoice Rental: ' . $e->getMessage());
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
            // Cari List invoice rental berdasarkan ID
            $list_invoice_rental = List_Invoice_Rental::find($id);

            // Jika tidak ditemukan
            if (!$list_invoice_rental) {
                Log::info('List Invoice Rental tidak ditemukan saat mencoba menghapus', ['id_invoice_rental' => $id]);

                return new ResponseApiResource(false, 'List Invoice Rental tidak ditemukan!', [], $list_invoice_rental, 404);
            }

            // Ubah status menjadi inactive
            $list_invoice_rental->update(['is_active_list_invoice_rental' => 'inactive']);

            // Soft delete
            $list_invoice_rental->delete();

            Log::info('List Invoice Rental berhasil dinonaktifkan', [
                'id_invoice_rental' => $id,
                'nomor_invoice' => $list_invoice_rental->nomor_invoice ?? null,
            ]);

            return new ResponseApiResource(true, 'List Invoice Rental berhasil dinonaktifkan!', $list_invoice_rental, 200);
        } catch (\Exception $e) {
            Log::error('Gagal menonaktifkan List Invoice Rental', [
                'id_invoice_rental' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }

    /**
     * Restore List Invoice Rental yang sudah dihapus (soft delete)
     */
    public function restore($id)
    {
        try {
            $list_invoice_rental = List_Invoice_Rental::withTrashed()->find($id);

            if (!$list_invoice_rental) {
                Log::info('List Invoice Rental tidak ditemukan saat mencoba dipulihkan', ['id_list_invoice_rental' => $id]);

                return new ResponseApiResource(false, 'List Invoice Rental tidak ditemukan!', [], $list_invoice_rental, 404);
            }

            $list_invoice_rental->update(['is_active_list_invoice_rental' => 'active']);

            $list_invoice_rental->restore();

            Log::info('List Invoice Rental berhasil dipulihkan', [
                'id_invoice_rental' => $id,
                'nomor_invoice' => $list_invoice_rental->nomor_invoice ?? null,
            ]);

            return new ResponseApiResource(true, 'List Invoice Rental berhasil dipulihkan!', $list_invoice_rental, 200);
        } catch (\Exception $e) {
            Log::error('Gagal memulihkan List Invoice Rental', [
                'id_invoice_rental' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }

    /**
     * Force delete (hapus permanen) List Invoice Rental
     */
    public function forceDestroy($id)
    {
        try {
            $list_invoice_rental = List_Invoice_Rental::withTrashed()->find($id);

            if (!$list_invoice_rental) {
                Log::info('List Invoice Rental tidak ditemukan saat mencoba hapus permanen', ['id_list_invoice_rental' => $id]);

                return new ResponseApiResource(false, 'List Invoice Rental tidak ditemukan!', [], $list_invoice_rental, 404);
            }

            $list_invoice_rental->update(['is_active_invoice_rental' => 'inactive']);

            $list_invoice_rental->forceDelete();

            Log::info('List Invoice Rental berhasil dihapus permanen', [
                'id_invoice_rental' => $id,
                'nomor_invoice' => $list_invoice_rental->nomor_invoice ?? null,
            ]);

            return new ResponseApiResource(true, 'List Invoice Rental berhasil dihapus permanen!', $list_invoice_rental, 200);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus permanen List Invoice Rental', [
                'id_invoice_rental' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }
}
