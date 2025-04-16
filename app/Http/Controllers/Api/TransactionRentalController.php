<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\List_Transaction_Rental;
use App\Models\Logging;
use App\Models\Transaction_Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Exception;

class TransactionRentalController extends Controller
{
    /**
     * index
     *
     * @return void
     */

    public function index()
    {
        try {
            // Dapatkan semua data kecuali yang memiliki is_active_transaction_rental = 'inactive'
            $transaction_rental = Transaction_Rental::where('is_active_transaction_rental', '!=', 'inactive')->latest()->get();

            Log::info('Sukses menampilkan data transaksi rental yang aktif');

            // Kembalikan data transaksi rental sebagai resource
            return new ResponseApiResource(true, 'Daftar Data transaksi rental Aktif', $transaction_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data transaksi rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data transaksi rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data transaksi rental Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            $transaction_rental =  Transaction_Rental::withTrashed()->latest()->get();

            Log::info('Sukses menampilkan data transaksi rental');

            // Kembalikan data transaksi rental sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data transaksi rental', $transaction_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data transaksi rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data transaksi rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data transaksi rental Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            $transaction_rental =  Transaction_Rental::onlyTrashed()->where('is_active_transaction_rental', '!=', 'active')->latest()->get();

            Log::info('Sukses menampilkan data transaksi rental yang dihapus');

            // Kembalikan data transaksi rental sebagai resource
            return new ResponseApiResource(true, 'Daftar data transaksi rental yang dihapus', $transaction_rental, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data transaksi rental Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data transaksi rental Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data transaksi rental Tidak Ditemukan!', null, $error->getMessage(), 404);
        }
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'id_kurir_transaction_rental' => 'required|exists:users,id_user',
                'id_branch_transaction_rental' => 'required|exists:branches,id_branch',
                'id_client_transaction_rental' => 'required|exists:clients,id_client',
                'recipient_name_transaction_rental' => 'required|string|max:255',
                'status_transaction_rental' => 'required|in:waiting for approval,approved,out,in,cancelled',
                'total_weight_transaction_rental' => 'required|numeric|min:0',
                'total_pcs_transaction_rental' => 'required|integer|min:1',
                'promo_transaction_rental' => 'nullable|numeric|min:0',
                'additional_cost_transaction_rental' => 'nullable|numeric|min:0',
                'total_price_transaction_rental' => 'required|numeric|min:0',
                'notes_transaction_laundry' => 'nullable|string',
                'is_active_transaction_rental' => 'required|in:active,inactive',
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Hitung total akhir transaksi
            // $totalTransaction = ($request->total_price_transaction_rental - ($request->promo_transaction_rental ?? 0)) + ($request->additional_cost_transaction_rental ?? 0);

            // Simpan data transaksi rental
            $transaction = Transaction_Rental::create([
                'id_kurir_transaction_rental' => $request->id_kurir_transaction_rental,
                'id_branch_transaction_rental' => $request->id_branch_transaction_rental,
                'id_client_transaction_rental' => $request->id_client_transaction_rental,
                'recipient_name_transaction_rental' => $request->recipient_name_transaction_rental,
                'status_transaction_rental' => $request->status_transaction_rental,
                'total_weight_transaction_rental' => $request->total_weight_transaction_rental,
                'total_pcs_transaction_rental' => $request->total_pcs_transaction_rental,
                'promo_transaction_rental' => $request->promo_transaction_rental ?? 0,
                'additional_cost_transaction_rental' => $request->additional_cost_transaction_rental ?? 0,
                'total_price_transaction_rental' => $request->total_price_transaction_rental,
                'notes_transaction_laundry' => $request->notes_transaction_laundry,
                'is_active_transaction_rental' => $request->is_active_transaction_rental,
                'first_date_transaction_rental' => Carbon::now(),
                'last_date_transaction_rental' => null,
            ]);

            Log::info('Transaksi rental berhasil ditambahkan dengan ID ' . $transaction->id_transaction_rental);

            return new ResponseApiResource(true, 'Transaksi rental berhasil ditambahkan!', $transaction, null, 201);
        } catch (ValidationException $error) {
            Log::error('Error validasi: ' . $error->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('Error saat menambahkan transaksi rental: ' . $e->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan transaksi rental.', $request->all(), $e->getMessage(), 500);
        }
    }

    public function storeListTransactionRental(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validasi transaksi utama
            $validator = Validator::make($request->all(), [
                'id_kurir_transaction_rental' => 'required|exists:users,id_user',
                'id_branch_transaction_rental' => 'required|exists:branches,id_branch',
                'id_client_transaction_rental' => 'required|exists:clients,id_client',
                'recipient_name_transaction_rental' => 'required|string|max:255',
                'status_transaction_rental' => 'required|in:pending,approved,returned,cancelled',
                'total_weight_transaction_rental' => 'required|numeric|min:0',
                'total_pcs_transaction_rental' => 'required|integer|min:0',
                'promo_transaction_rental' => 'nullable|numeric|min:0',
                'additional_cost_transaction_rental' => 'nullable|numeric|min:0',
                'total_price_transaction_rental' => 'required|numeric|min:0',
                'notes_transaction_laundry' => 'nullable|string',
                'is_active_transaction_rental' => 'required|in:active,inactive',
                'first_date_transaction_rental' => 'required|date',
                'last_date_transaction_rental' => 'required|date',
                'list_transaction_rentals' => 'required|array|min:1',
                'list_transaction_rentals.*.id_item_rental' => 'required|exists:rental_items,id_rental_item',
                'list_transaction_rentals.*.status_list_transaction_rental' => 'required|in:rented,returned',
                'list_transaction_rentals.*.condition_list_transaction_rental' => 'required|in:clean,dirty,damaged',
                'list_transaction_rentals.*.note_list_transaction_rental' => 'nullable|string',
                'list_transaction_rentals.*.price_list_transaction_rental' => 'required|numeric|min:0',
                'list_transaction_rentals.*.weight_list_transaction_rental' => 'required|numeric|min:0',
                'list_transaction_rentals.*.is_active_list_transaction_rental' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                return new ResponseApiResource(false, 'Validasi transaksi rental gagal', $request->all(), $validator->errors());
            }

            // Hitung total transaksi
            $totalTransaction = ($request->total_price_transaction_rental - ($request->promo_transaction_rental ?? 0)) + ($request->additional_cost_transaction_rental ?? 0);

            // Buat transaksi utama
            $transaction_rental = Transaction_Rental::create([
                'id_kurir_transaction_rental' => $request->id_kurir_transaction_rental,
                'id_branch_transaction_rental' => $request->id_branch_transaction_rental,
                'id_client_transaction_rental' => $request->id_client_transaction_rental,
                'recipient_name_transaction_rental' => $request->recipient_name_transaction_rental,
                'status_transaction_rental' => $request->status_transaction_rental,
                'total_weight_transaction_rental' => $request->total_weight_transaction_rental,
                'total_pcs_transaction_rental' => $request->total_pcs_transaction_rental,
                'promo_transaction_rental' => $request->promo_transaction_rental ?? 0,
                'additional_cost_transaction_rental' => $request->additional_cost_transaction_rental ?? 0,
                'total_price_transaction_rental' => $request->total_price_transaction_rental,
                'total_transaction_rental' => $totalTransaction,
                'notes_transaction_laundry' => $request->notes_transaction_laundry,
                'is_active_transaction_rental' => $request->is_active_transaction_rental,
                'first_date_transaction_rental' => $request->first_date_transaction_rental,
                'last_date_transaction_rental' => $request->last_date_transaction_rental,
            ]);

            // Buat daftar list transaksi
            $list_transactions = [];
            foreach ($request->list_transaction_rentals as $list) {
                $list_transactions[] = List_Transaction_Rental::create([
                    'id_rental_transaction' => $transaction_rental->id_transaction_rental,
                    'id_item_rental' => $list['id_item_rental'],
                    'status_list_transaction_rental' => $list['status_list_transaction_rental'],
                    'condition_list_transaction_rental' => $list['condition_list_transaction_rental'],
                    'note_list_transaction_rental' => $list['note_list_transaction_rental'] ?? null,
                    'price_list_transaction_rental' => $list['price_list_transaction_rental'],
                    'weight_list_transaction_rental' => $list['weight_list_transaction_rental'],
                    'is_active_list_transaction_rental' => $list['is_active_list_transaction_rental'],
                ]);
            }

            DB::commit();

            Log::info('Transaksi rental berhasil ditambahkan dengan ID: ' . $transaction_rental->id_transaction_rental);

            return new ResponseApiResource(true, 'Transaksi rental berhasil ditambahkan!', [
                'transaction' => $transaction_rental,
                'list_transactions' => $list_transactions,
            ], null, 201);
        } catch (ValidationException $error) {
            DB::rollBack();
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan transaksi rental: ' . $e->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan saat menyimpan transaksi rental.', $request->all(), $e->getMessage(), 500);
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
            $transaction_rental = Transaction_Rental::withTrashed()->find($id);

            // Periksa apakah Transaksi rental ditemukan
            if (!$transaction_rental) {
                Log::info('Transaksi rental tidak ditemukan dengan id ' . $id);

                return new ResponseApiResource(false, 'Transaksi rental tidak ditemukan', null, 404);
            }

            // Log info jika Transaksi rental ditemukan
            Log::info('Detail Transaksi rental ditemukan', ['id' => $transaction_rental->id_transaction_rental, 'id_kurir_transaction_rental' => $transaction_rental->id_kurir_transaction_rental]);

            // Return data Transaksi rental sebagai resource
            return new ResponseApiResource(true, 'Detail Data Transaksi rental', $transaction_rental, null, 200);
        } catch (Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Gagal mengambil data Transaksi rental', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }

    public function getListWithTransactionRental($id)
    {
        try {
            // Cari data transaksi laundry berdasarkan ID, termasuk yang sudah dihapus (soft deleted)
            $transaction_rental = Transaction_Rental::withTrashed()->with('listTransactionRentals')->find($id);

            // Periksa apakah transaksi laundry ditemukan
            if (!$transaction_rental) {
                Log::info('Transaksi laundry dengan ID ' . $id . ' tidak ditemukan.');

                return new ResponseApiResource(false, 'Transaksi laundry tidak ditemukan.', null, 404);
            }

            // Log informasi jika transaksi laundry ditemukan
            Log::info('Detail transaksi laundry ditemukan.', [
                'id' => $transaction_rental->id_transaction_rental,
                'id_kurir_transaction_rental' => $transaction_rental->id_kurir_transaction_rental
            ]);

            // Kembalikan data transaksi laundry sebagai response
            return new ResponseApiResource(true, 'Detail transaksi laundry berhasil ditemukan.', $transaction_rental, null, 200);
        } catch (Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Gagal mengambil data transaksi laundry.', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server.', null, 500);
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
            // Validasi input
            $validator = Validator::make($request->all(), [
                'id_kurir_transaction_rental' => 'required|exists:users,id_user',
                'id_branch_transaction_rental' => 'required|exists:branches,id_branch',
                'id_client_transaction_rental' => 'required|exists:clients,id_client',
                'recipient_name_transaction_rental' => 'required|string|max:255',
                'status_transaction_rental' => 'required|in:waiting for approval,approved,out,in,cancelled',
                'total_weight_transaction_rental' => 'required|numeric|min:0',
                'total_pcs_transaction_rental' => 'required|integer|min:1',
                'promo_transaction_rental' => 'nullable|numeric|min:0',
                'additional_cost_transaction_rental' => 'nullable|numeric|min:0',
                'total_price_transaction_rental' => 'required|numeric|min:0',
                'notes_transaction_laundry' => 'nullable|string',
                'is_active_transaction_rental' => 'required|in:active,inactive',
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Cari transaksi laundry berdasarkan ID
            $transaction = Transaction_Rental::withTrashed()->find($id);
            if (!$transaction) {
                return new ResponseApiResource(false, 'Transaksi laundry tidak ditemukan.', [], null, 404);
            }

            $data = [
                'id_kurir_transaction_rental' => $request->id_kurir_transaction_rental,
                'id_branch_transaction_rental' => $request->id_branch_transaction_rental,
                'id_client_transaction_rental' => $request->id_client_transaction_rental,
                'recipient_name_transaction_rental' => $request->recipient_name_transaction_rental,
                'status_transaction_rental' => $request->status_transaction_rental,
                'total_weight_transaction_rental' => $request->total_weight_transaction_rental,
                'total_pcs_transaction_rental' => $request->total_pcs_transaction_rental,
                'promo_transaction_rental' => $request->promo_transaction_rental ?? 0,
                'additional_cost_transaction_rental' => $request->additional_cost_transaction_rental ?? 0,
                'total_price_transaction_rental' => $request->total_price_transaction_rental,
                'notes_transaction_laundry' => $request->notes_transaction_laundry,
                'is_active_transaction_rental' => $request->is_active_transaction_rental,
                'first_date_transaction_rental' => $transaction->first_date_transaction_rental
            ];

            // Jika ada input status_transaction_rental = "completed", update last_date_transaction_rental
            if ($request->status_transaction_rental === "in") {
                // Update last_date_transaction_rental
                $data['last_date_transaction_rental'] = Carbon::now();

                // Logging berhasil
                Log::info('Transaksi laundry dengan id ' . $id . ' berhasil diselesaikan.');
            } elseif ($request->status_transaction_rental === "cancelled") { // Jika ada input status_transaction_rental = "cancelled", update last_date_transaction_rental dan non-aktifkan transaksi laundry
                // Non-aktifkan transaksi laundry & update last_date_transaction_rental
                $data['is_active_transaction_rental'] = "inactive";
                $data['last_date_transaction_rental'] = Carbon::now();

                // Logging berhasil
                Log::info('Transaksi laundry dengan id ' . $id . ' berhasil dibatalkan.');
            } else {
                // Logging berhasil
                Log::info('Transaksi laundry dengan id ' . $id . ' berhasil diperbarui.');

                $data['is_active_transaction_rental'] = "active";
                $data['last_date_transaction_rental'] = null;
            }

            // Simpan data transaksi laundry
            $transaction->update($data); // Simpan data transaksi laundry

            Log::info('Transaksi laundry berhasil diperbarui dengan ID ' . $transaction->id_transaction_rental);

            return new ResponseApiResource(true, 'Transaksi laundry berhasil diperbarui!', $transaction, null, 200);
        } catch (ValidationException $error) {
            Log::error('Error validasi: ' . $error->getMessage());  // Log error validasi

            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);   // Kembalikan response error validasi
        } catch (Exception $e) {
            Log::error('Error saat memperbarui transaksi laundry: ' . $e->getMessage());  // Log error saat memperbarui transaksi laundry

            return new ResponseApiResource(false, 'Terjadi kesalahan saat memperbarui transaksi laundry.', $request->all(), $e->getMessage(), 500);  // Kembalikan response error
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
            $transaction_rental = Transaction_Rental::find($id);

            // Jika cabang tidak ditemukan
            if (!$transaction_rental) {
                Log::info('Cabang tidak ditemukan saat mencoba menghapus', ['id_transaction_rental' => $id]);

                return new ResponseApiResource(false, 'Cabang tidak ditemukan!', [],  $transaction_rental, 404);
            }

            // Ubah status is_active_transaction_rental menjadi 'inactive'
            $transaction_rental->update(['is_active_transaction_rental' => 'inactive']);

            // Hapus cabang
            $transaction_rental->delete();

            // Log informasi perubahan status Cabang
            Log::info('Cabang berhasil dinonaktifkan', ['id_transaction_rental' => $id, 'name_client_transaction_rental' => $transaction_rental->name_client_transaction_rental]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Cabang berhasil dinonaktifkan!', $transaction_rental, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal menonaktifkan Cabang', [
                'id_branch' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }

    public function restore($id)
    {
        try {
            // Cari data berdasarkan ID
            $transaction_rental = Transaction_Rental::withTrashed()->find($id);

            // Jika Transaksi Laundry tidak ditemukan
            if (!$transaction_rental) {
                Log::info('Transaksi Laundry tidak ditemukan saat mencoba dipulihkan', ['id_transaction_rental' => $id]);

                return new ResponseApiResource(false, 'Transaksi Laundry tidak ditemukan!', [],  $transaction_rental, 404);
            }

            // Ubah status is_active_transaction_rental menjadi 'active'
            $transaction_rental->update(['is_active_transaction_rental' => 'active']);

            // Pulihkan Transaksi Laundry
            $transaction_rental->restore();

            // Log informasi perubahan status Transaksi Laundry
            Log::info('Transaksi Laundry berhasil dipulihkan', ['id_transaction_rental' => $id, 'name_client_transaction_rental' => $transaction_rental->name_client_transaction_rental]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Transaksi Laundry berhasil dipulihkan!', $transaction_rental, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal memulihkan Transaksi Laundry', [
                'id_branch' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }

    public function forceDestroy($id)
    {
        try {
            // Cari Transaksi Laundry berdasarkan ID
            $transaction_rental = Transaction_Rental::withTrashed()->find($id);

            // Jika Transaksi Laundry tidak ditemukan
            if (!$transaction_rental) {
                Log::info('Transaksi Laundry tidak ditemukan saat mencoba hapus permanent', ['id_transaction_rental' => $id]);

                return new ResponseApiResource(false, 'Transaksi Laundry tidak ditemukan!', $transaction_rental,  $transaction_rental, 404);
            }

            // Ubah status is_active_transaction_rental menjadi 'inactive'
            $transaction_rental->update(['is_active_transaction_rental' => 'inactive']);

            // Hapus Transaksi Laundry permanent
            $transaction_rental->forceDelete();

            // Log informasi perubahan status Transaksi Laundry
            Log::info('Transaksi Laundry berhasil hapus permanent', ['id_transaction_rental' => $id, 'name_client_transaction_rental' => $transaction_rental->name_client_transaction_rental]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Transaksi Laundry berhasil hapus permanent!', $transaction_rental, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal hapus permanent Transaksi Laundry', [
                'id_branch' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }
}
