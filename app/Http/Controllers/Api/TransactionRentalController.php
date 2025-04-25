<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\List_Transaction_Rental;
use App\Models\Logging;
use App\Models\Rental_Item;
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
        DB::beginTransaction();
        try {
            // Validasi transaksi utama
            $validator = Validator::make($request->all(), [
                'id_kurir_transaction_rental' => 'required|exists:users,id_user',
                'id_branch_transaction_rental' => 'required|exists:branches,id_branch',
                'id_client_transaction_rental' => 'required|exists:clients,id_client',
                'recipient_name_transaction_rental' => 'nullable|string|max:255',
                'type_rental_transaction' => 'required|in:bath towel,hand towel,gorden,keset',
                'status_transaction_rental' => 'required|in:waiting for approval,approved,out,in,returned,cancelled',
                'price_weight_transaction_rental' => 'required|integer|min:0',
                'promo_transaction_rental' => 'nullable|numeric|min:0',
                'additional_cost_transaction_rental' => 'nullable|numeric|min:0',
                'notes_transaction_laundry' => 'nullable|string',
                'list_transaction_rentals' => 'required|array|min:1',
                'list_transaction_rentals.*.id_item_rental' => 'required|exists:rental_items,id_rental_item',
                'list_transaction_rentals.*.condition_list_transaction_rental' => 'required|in:clean,dirty,damaged',
                'list_transaction_rentals.*.note_list_transaction_rental' => 'nullable|string',
                'list_transaction_rentals.*.price_list_transaction_rental' => 'required|numeric|min:0',
                'list_transaction_rentals.*.weight_list_transaction_rental' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                DB::rollBack();
                return new ResponseApiResource(false, 'Validasi transaksi rental gagal', $request->all(), $validator->errors());
            }

            // Hitung total berat dari semua item
            $total_weight = 0;
            foreach ($request->list_transaction_rentals as $list) {
                $total_weight += $list['weight_list_transaction_rental'];
            }

            // Hitung total harga berdasarkan berat × harga per berat
            $price_per_weight = $request->price_weight_transaction_rental;
            $total_price = $price_per_weight * $total_weight;

            // Hitung total transaksi akhir (setelah promo dan tambahan)
            $promo = $request->promo_transaction_rental ?? 0;
            $additional = $request->additional_cost_transaction_rental ?? 0;
            $total_transaction = $total_price - $promo + $additional;

            // Hitung total harga akhir invoice setelah promo dan biaya tambahan

            // Buat transaksi utama
            $transaction_rental = Transaction_Rental::create([
                'id_kurir_transaction_rental' => $request->id_kurir_transaction_rental,
                'id_branch_transaction_rental' => $request->id_branch_transaction_rental,
                'id_client_transaction_rental' => $request->id_client_transaction_rental,
                'recipient_name_transaction_rental' => $request->recipient_name_transaction_rental,
                'type_rental_transaction' => $request->type_rental_transaction,
                'status_transaction_rental' => $request->status_transaction_rental,
                'price_weight_transaction_rental' => $price_per_weight,
                'total_weight_transaction_rental' => $total_weight,
                'total_pcs_transaction_rental' => count($request->list_transaction_rentals),
                'promo_transaction_rental' => $request->promo_transaction_rental ?? 0,
                'additional_cost_transaction_rental' => $additional,
                'total_price_transaction_rental' => $total_transaction,
                'notes_transaction_laundry' => $request->notes_transaction_laundry,
                'is_active_transaction_rental' => 'active',
            ]);

            // Buat daftar list transaksi
            $list_transactions = [];
            foreach ($request->list_transaction_rentals as $list) {
                // Ambil data item rental berdasarkan id
                $rental_item = Rental_Item::findOrFail($list['id_item_rental']);

                // Hitung total berat dan total harga
                $weight = $list['weight_list_transaction_rental'];
                $price = $rental_item->price_rental_item;

                $list_transaction = List_Transaction_Rental::create([
                    'id_rental_transaction' => $transaction_rental->id_transaction_rental,
                    'id_item_rental' => $list['id_item_rental'],
                    'status_list_transaction_rental' => 'rented',
                    'condition_list_transaction_rental' => $list['condition_list_transaction_rental'],
                    'note_list_transaction_rental' => $list['note_list_transaction_rental'] ?? null,
                    'price_list_transaction_rental' => $price,
                    'weight_list_transaction_rental' => $weight,
                    'is_active_list_transaction_rental' => 'active',
                ]);

                // Update status item rental menjadi 'rented'
                Rental_Item::where('id_rental_item', $list['id_item_rental'])->update([
                    'status_rental_item' => 'rented'
                ]);

                // Simpan ke array hasil jika diperlukan
                $list_transactions[] = $list_transaction;
            }

            DB::commit();

            Log::info('Transaksi rental berhasil ditambahkan dengan ID: ' . $transaction_rental->id_transaction_rental);

            $mergedTransaction = $transaction_rental;
            $mergedTransaction['list_transaction_rentals'] = $list_transactions;

            return new ResponseApiResource(
                true,
                'Transaksi rental berhasil ditambahkan!',
                $mergedTransaction,
                null,
                201
            );
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
                'recipient_name_transaction_rental' => 'nullable|string|max:255',
                'type_rental_transaction' => 'required|in:bath towel,hand towel,gorden,keset',
                'status_transaction_rental' => 'required|in:waiting for approval,approved,out,in,cancelled',
                'price_weight_transaction_rental' => 'required|integer|min:0',
                'total_weight_transaction_rental' => 'required|numeric|min:0',
                'total_pcs_transaction_rental' => 'required|integer|min:1',
                'promo_transaction_rental' => 'nullable|numeric|min:0',
                'additional_cost_transaction_rental' => 'nullable|numeric|min:0',
                'notes_transaction_laundry' => 'nullable|string',
                'is_active_transaction_rental' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Cari transaksi rental berdasarkan ID
            $transaction = Transaction_Rental::withTrashed()->find($id);
            if (!$transaction) {
                Log::warning('Transaksi rental tidak ditemukan dengan ID ' . $id);
                return new ResponseApiResource(false, 'Transaksi rental tidak ditemukan.', [], null, 404);
            }

            // Hitung total harga berdasarkan berat × harga per berat
            $total_weight = $request->total_weight_transaction_rental;
            $price_per_weight = $request->price_weight_transaction_rental;
            $total_price = $price_per_weight * $total_weight;

            // Hitung total transaksi akhir (setelah promo dan tambahan)
            $promo = $request->promo_transaction_rental ?? 0;
            $additional = $request->additional_cost_transaction_rental ?? 0;
            $total_transaction = $total_price - $promo + $additional;

            $data = [
                'id_kurir_transaction_rental' => $request->id_kurir_transaction_rental,
                'id_branch_transaction_rental' => $request->id_branch_transaction_rental,
                'id_client_transaction_rental' => $request->id_client_transaction_rental,
                'recipient_name_transaction_rental' => $request->recipient_name_transaction_rental,
                'status_transaction_rental' => $request->status_transaction_rental,
                'type_rental_transaction' => $request->type_rental_transaction,
                'price_weight_transaction_rental' => $price_per_weight,
                'total_weight_transaction_rental' => $total_weight,
                'total_pcs_transaction_rental' => $request->total_pcs_transaction_rental,
                'promo_transaction_rental' => $promo,
                'additional_cost_transaction_rental' => $additional,
                'total_price_transaction_rental' => $total_transaction,
                'notes_transaction_laundry' => $request->notes_transaction_laundry,
                'is_active_transaction_rental' => $request->is_active_transaction_rental,
            ];

            if ($request->status_transaction_rental === "cancelled") {
                $data['is_active_transaction_rental'] = "inactive";
                $transaction->delete();
                Log::info('Transaksi rental dengan id ' . $id . ' berhasil dibatalkan.');
            } else {
                $data['is_active_transaction_rental'] = "active";
                $transaction->restore();
                Log::info('Transaksi rental dengan id ' . $id . ' berhasil diperbarui.');
            }

            $transaction->update($data);

            Log::info('Transaksi rental berhasil diperbarui dengan ID ' . $transaction->id_transaction_rental);

            return new ResponseApiResource(true, 'Transaksi rental berhasil diperbarui!', $transaction, null, 200);
        } catch (ValidationException $error) {
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('Error saat memperbarui transaksi rental: ' . $e->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan saat memperbarui transaksi rental.', $request->all(), $e->getMessage(), 500);
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
