<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\List_Transaction_Rental;
use App\Models\Logging;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ListTransactionRentalController extends Controller
{
    /**
     * index
     *
     * @return void
     */

    public function index()
    {
        try {
            // Dapatkan semua data kecuali yang memiliki is_active_list_transaction_rental = 'inactive'
            $list_transaction_rental = List_Transaction_Rental::where('is_active_list_transaction_rental', '!=', 'inactive')->latest()->get();

            Log::info('Sukses menampilkan data list transaksi rental yang aktif');

            // Kembalikan data list transaksi rental sebagai resource
            return new ResponseApiResource(true, 'Daftar Data list transaksi rental Aktif', $list_transaction_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data list transaksi rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data list transaksi rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data list transaksi rental Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            $list_transaction_rental =  List_Transaction_Rental::withTrashed()->latest()->get();

            Log::info('Sukses menampilkan data list transaksi rental');

            // Kembalikan data list transaksi rental sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data list transaksi rental', $list_transaction_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data list transaksi rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data list transaksi rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data list transaksi rental Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            $list_transaction_rental =  List_Transaction_Rental::onlyTrashed()->where('is_active_list_transaction_rental', '!=', 'active')->latest()->get();

            Log::info('Sukses menampilkan data list transaksi rental yang dihapus');

            // Kembalikan data list transaksi rental sebagai resource
            return new ResponseApiResource(true, 'Daftar data list transaksi rental yang dihapus', $list_transaction_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data list transaksi rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data list transaksi rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data list transaksi rental Tidak Ditemukan!', null, $error->getMessage(), 404);
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
                'id_rental_transaction' => 'required|exists:transaction_rentals,id_transaction_rental',
                'id_item_rental' => 'required|exists:rental_items,id_rental_item',
                'status_list_transaction_rental' => 'required|in:rented,returned,cancelled',
                'condition_list_transaction_rental' => 'required|in:clean,dirty,damaged',
                'note_list_transaction_rental' => 'nullable|string',
                'price_list_transaction_rental' => 'required|numeric|min:0',
                'weight_list_transaction_rental' => 'required|numeric|min:0',
                'is_active_list_transaction_rental' => 'required|in:active,inactive',
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Membuat list transaksi rental baru
            $list_transaction_rental = List_Transaction_Rental::create([
                'id_rental_transaction' => $request->id_rental_transaction,
                'id_item_rental' => $request->id_item_rental,
                'status_list_transaction_rental' => $request->status_list_transaction_rental,
                'condition_list_transaction_rental' => $request->condition_list_transaction_rental,
                'note_list_transaction_rental' => $request->note_list_transaction_rental,
                'price_list_transaction_rental' => $request->price_list_transaction_rental,
                'weight_list_transaction_rental' => $request->weight_list_transaction_rental,
                'is_active_list_transaction_rental' => $request->is_active_list_transaction_rental,
            ]);

            // Kembalikan response sukses
            Log::info('List transaksi rental berhasil ditambahkan dengan id ' . $list_transaction_rental->id_list_transaction_rental);

            return new ResponseApiResource(true, 'List transaksi rental berhasil ditambahkan!', $list_transaction_rental, null, 201);
        } catch (ValidationException $error) {
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('Error saat menambahkan list transaksi rental: ' . $e->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan list transaksi rental.', $request->all(), $e->getMessage(), 500);
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
            $list_transaction_rental = List_Transaction_Rental::withTrashed()->find($id);

            // Periksa apakah List Transaksi rental ditemukan
            if (!$list_transaction_rental) {
                Log::info('List Transaksi rental tidak ditemukan dengan id ' . $id);

                return new ResponseApiResource(false, 'List Transaksi rental tidak ditemukan', null, 404);
            }

            // Log info jika List Transaksi rental ditemukan
            Log::info('Detail List Transaksi rental ditemukan', ['id' => $list_transaction_rental->id_list_transaction_rental, 'id_transaction_rental' => $list_transaction_rental->id_transaction_rental]);

            // Return data List Transaksi rental sebagai resource
            return new ResponseApiResource(true, 'Detail Data List Transaksi rental : ', $list_transaction_rental, null, 200);
        } catch (Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Gagal mengambil data List Transaksi rental', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
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
            // Cari list transaksi rental berdasarkan ID (termasuk yang soft deleted)
            $list_transaction_rental = List_Transaction_Rental::withTrashed()->find($id);
            if (!$list_transaction_rental) {
                return new ResponseApiResource(false, 'List transaksi laundry tidak ditemukan.', [], null, 404);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'id_rental_transaction' => 'required|exists:transaction_rentals,id_transaction_rental',
                'id_item_rental' => 'required|exists:rental_items,id_rental_item',
                'status_list_transaction_rental' => 'required|in:rented,returned,cancelled',
                'condition_list_transaction_rental' => 'required|in:clean,dirty,damaged',
                'note_list_transaction_rental' => 'nullable|string',
                'price_list_transaction_rental' => 'required|numeric|min:0',
                'weight_list_transaction_rental' => 'required|numeric|min:0',
                'is_active_list_transaction_rental' => 'required|in:active,inactive',
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Siapkan data yang akan diperbarui
            $data = [
                'id_rental_transaction' => $request->id_rental_transaction,
                'id_item_rental' => $request->id_item_rental,
                'status_list_transaction_rental' => $request->status_list_transaction_rental,
                'condition_list_transaction_rental' => $request->condition_list_transaction_rental,
                'note_list_transaction_rental' => $request->note_list_transaction_rental,
                'price_list_transaction_rental' => $request->price_list_transaction_rental,
                'weight_list_transaction_rental' => $request->weight_list_transaction_rental,
                'is_active_list_transaction_rental' => $request->is_active_list_transaction_rental,
            ];

            // Jika ada input status_list_transaction_rental = "completed", update last_date_transaction_laundry
            if ($request->status_list_transaction_rental === "cancelled") {
                $data['is_active_list_transaction_rental'] = 'inactive';
            }

            // Update data transaksi rental
            $list_transaction_rental->update($data);

            // Jika status transaksi rental diubah menjadi "inactive", maka soft delete data
            if ($list_transaction_rental->is_active_list_transaction_rental === 'inactive') {
                $list_transaction_rental->delete();

                Log::info('List transaksi rental berhasil dinonaktifkan', ['id_list_transaction_rental' => $id]);
            } elseif ($list_transaction_rental->is_active_list_transaction_rental === 'active') {
                $list_transaction_rental->restore();

                // Logging berhasil
                Log::info('List transaksi rental berhasil diaktifkan', ['id_list_transaction_rental' => $id]);
            }
            // Logging berhasil
            Log::info('List transaksi rental dengan id ' . $id . ' berhasil diperbarui.');

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'List transaksi rental berhasil diperbarui!', $list_transaction_rental, null, 200);
        } catch (ValidationException $error) {
            // Logging error validasi
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            // Logging error umum
            Log::error('Error saat memperbarui list transaksi rental: ' . $e->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan saat memperbarui list transaksi rental.', $request->all(), $e->getMessage(), 500);
        }
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return void
     */
    public function destroy($id)
    {
        try {
            // Cari cabang berdasarkan ID
            $list_transaction_rental = List_Transaction_Rental::find($id);

            // Jika cabang tidak ditemukan
            if (!$list_transaction_rental) {
                Log::info('Cabang tidak ditemukan saat mencoba menghapus', ['id_transaction_rental' => $id]);

                return new ResponseApiResource(false, 'Cabang tidak ditemukan!', $id,  $list_transaction_rental, 404);
            }

            // Ubah status is_active_list_transaction_rental menjadi 'inactive'
            $list_transaction_rental->update(['is_active_list_transaction_rental' => 'inactive']);

            // Hapus cabang
            $list_transaction_rental->delete();

            // Log informasi perubahan status Cabang
            Log::info('Cabang berhasil dinonaktifkan', ['id_transaction_rental' => $id, 'id_transaction_rental' => $list_transaction_rental->id_transaction_rental]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Cabang berhasil dinonaktifkan!', $list_transaction_rental, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal menonaktifkan Cabang', [
                'id_branch' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }

    public function restore($id)
    {
        try {
            // Cari data berdasarkan ID
            $list_transaction_rental = List_Transaction_Rental::withTrashed()->find($id);

            // Jika Transaksi rental tidak ditemukan
            if (!$list_transaction_rental) {
                Log::info('Transaksi rental tidak ditemukan saat mencoba dipulihkan', ['id_transaction_rental' => $id]);

                return new ResponseApiResource(false, 'Transaksi rental tidak ditemukan!', $id,  $list_transaction_rental, 404);
            }

            // Ubah status is_active_list_transaction_rental menjadi 'active'
            $list_transaction_rental->update(['is_active_list_transaction_rental' => 'active']);

            // Pulihkan Transaksi rental
            $list_transaction_rental->restore();

            // Log informasi perubahan status Transaksi rental
            Log::info('Transaksi rental berhasil dipulihkan', ['id_transaction_rental' => $id, 'id_transaction_rental' => $list_transaction_rental->id_transaction_rental]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Transaksi rental berhasil dipulihkan!', $list_transaction_rental, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal memulihkan Transaksi rental', [
                'id_branch' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }

    public function forceDestroy($id)
    {
        try {
            // Cari Transaksi rental berdasarkan ID
            $list_transaction_rental = List_Transaction_Rental::withTrashed()->find($id);

            // Jika Transaksi rental tidak ditemukan
            if (!$list_transaction_rental) {
                Log::info('Transaksi rental tidak ditemukan saat mencoba hapus permanent', ['id_transaction_rental' => $id]);

                return new ResponseApiResource(false, 'Transaksi rental tidak ditemukan!', $id,  $list_transaction_rental, 404);
            }

            // Ubah status is_active_list_transaction_rental menjadi 'inactive'
            $list_transaction_rental->update(['is_active_list_transaction_rental' => 'inactive']);

            // Hapus Transaksi rental permanent
            $list_transaction_rental->forceDelete();

            // Log informasi perubahan status Transaksi rental
            Log::info('Transaksi rental berhasil hapus permanent', ['id_transaction_rental' => $id, 'id_transaction_rental' => $list_transaction_rental->id_transaction_rental]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Transaksi rental berhasil hapus permanent!', $list_transaction_rental, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal hapus permanent Transaksi rental', [
                'id_branch' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }
}
