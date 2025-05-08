<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\Laundry_Item;
use App\Models\List_Transaction_Laundry;
use App\Models\Logging;
use App\Models\Transaction_Laundry;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TransactionLaundryController extends Controller
{
    /**
     * index
     *
     * @return void
     */

    public function index()
    {
        try {
            // Dapatkan semua data kecuali yang memiliki is_active_transaction_laundry = 'inactive'
            $transaction_laundry = Transaction_Laundry::where('is_active_transaction_laundry', '!=', 'inactive')->latest()->get();

            Log::info('Sukses menampilkan data transaksi laundry yang aktif');

            // Kembalikan data transaksi laundry sebagai resource
            return new ResponseApiResource(true, 'Daftar Data transaksi laundry Aktif', $transaction_laundry, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data transaksi laundry Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data transaksi laundry Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data transaksi laundry Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            $transaction_laundry =  Transaction_Laundry::withTrashed()->latest()->get();

            Log::info('Sukses menampilkan data transaksi laundry');

            // Kembalikan data transaksi laundry sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data transaksi laundry', $transaction_laundry, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data transaksi laundry Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data transaksi laundry Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data transaksi laundry Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            $transaction_laundry =  Transaction_Laundry::onlyTrashed()->where('is_active_transaction_laundry', '!=', 'active')->latest()->get();

            Log::info('Sukses menampilkan data transaksi laundry yang dihapus');

            // Kembalikan data transaksi laundry sebagai resource
            return new ResponseApiResource(true, 'Daftar data transaksi laundry yang dihapus', $transaction_laundry, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data transaksi laundry Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data transaksi laundry Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data transaksi laundry Tidak Ditemukan!', null, $error->getMessage(), 404);
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
        DB::beginTransaction();
        try {
            // Validasi input transaksi utama
            $validator = Validator::make($request->all(), [
                'id_user_transaction_laundry' => 'required|exists:users,id_user',
                'id_branch_transaction_laundry' => 'required|exists:branches,id_branch',
                'name_client_transaction_laundry' => 'required|string|max:255',
                // 'status_transaction_laundry' => 'required|in:pending,in_progress,completed,cancelled',
                'notes_transaction_laundry' => 'nullable|string',
                'promo_transaction_laundry' => 'nullable|numeric|min:0',
                'additional_cost_transaction_laundry' => 'nullable|numeric|min:0',
                'cash_transaction_laundry' => 'required|numeric|min:0',
                'list_transaction_laundry' => 'required|array|min:1',
                'list_transaction_laundry.*.id_item_laundry' => 'required|exists:laundry_items,id_laundry_item',
                'list_transaction_laundry.*.weight_list_transaction_laundry' => 'required|numeric|min:0',
                // 'list_transaction_laundry.*.status_list_transaction_laundry' => 'required|in:pending,completed,cancelled',
                'list_transaction_laundry.*.note_list_transaction_laundry' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                return new ResponseApiResource(false, 'Validasi transaksi laundry gagal', $request->all(), $validator->errors());
            }

            $total_weight = 0;
            $total_price = 0;
            $count_item = 0;

            // Untuk menyimpan semua transaksi list
            $list_transactions = [];

            foreach ($request->list_transaction_laundry as $list) {
                $item_laundry = Laundry_Item::findOrFail($list['id_item_laundry']);
                $price_per_weight = $item_laundry->price_laundry_item;

                $weight = $list['weight_list_transaction_laundry'];
                $price = $price_per_weight * $weight;

                $total_weight += $weight;
                $total_price += $price;
                $count_item++;

                $list_transactions[] = [
                    'id_item_laundry' => $list['id_item_laundry'],
                    'price_list_transaction_laundry' => $price_per_weight,
                    'weight_list_transaction_laundry' => $weight,
                    'total_price_list_transaction_laundry' => $price,
                    'status_list_transaction_laundry' => 'pending',
                    'note_list_transaction_laundry' => $list['note_list_transaction_laundry'] ?? null,
                ];
            }

            $promo = $request->promo_transaction_laundry ?? 0;
            $additional_cost = $request->additional_cost_transaction_laundry ?? 0;
            $totalTransaction = ($total_price - $promo) + $additional_cost;
            $changeMoney = $request->cash_transaction_laundry - $totalTransaction;

            if ($changeMoney < 0) {
                DB::rollBack();
                return new ResponseApiResource(false, 'Uang pembayaran kurang!', $request->all(), ['cash_transaction_laundry' => 'Uang tidak mencukupi untuk membayar total transaksi'], 400);
            }

            // Buat transaksi utama
            $transaction_laundry = Transaction_Laundry::create([
                'id_user_transaction_laundry' => $request->id_user_transaction_laundry,
                'id_branch_transaction_laundry' => $request->id_branch_transaction_laundry,
                'name_client_transaction_laundry' => $request->name_client_transaction_laundry,
                'status_transaction_laundry' => 'pending',
                'notes_transaction_laundry' => $request->notes_transaction_laundry,
                'total_weight_transaction_laundry' => $total_weight,
                'total_price_transaction_laundry' => $total_price,
                'count_item_laundry_transaction_laundry' => $count_item,
                'promo_transaction_laundry' => $promo,
                'additional_cost_transaction_laundry' => $additional_cost,
                'total_transaction_laundry' => $totalTransaction,
                'cash_transaction_laundry' => $request->cash_transaction_laundry,
                'change_money_transaction_laundry' => $changeMoney,
                'is_active_transaction_laundry' => 'active',
                'first_date_transaction_laundry' => Carbon::now(),
                'last_date_transaction_laundry' => null,
            ]);

            // Simpan semua detail list transaksi
            foreach ($list_transactions as $list) {
                List_Transaction_Laundry::create([
                    'id_transaction_laundry' => $transaction_laundry->id_transaction_laundry,
                    'id_item_laundry' => $list['id_item_laundry'],
                    'price_list_transaction_laundry' => $list['price_list_transaction_laundry'],
                    'weight_list_transaction_laundry' => $list['weight_list_transaction_laundry'],
                    'total_price_list_transaction_laundry' => $list['total_price_list_transaction_laundry'],
                    'status_list_transaction_laundry' => $list['status_list_transaction_laundry'],
                    'note_list_transaction_laundry' => $list['note_list_transaction_laundry'],
                    'is_active_list_transaction_laundry' => 'active',
                ]);
            }

            DB::commit();

            Log::info('Transaksi laundry berhasil ditambahkan dengan id ' . $transaction_laundry->id_transaction_laundry);

            $mergedTransaction = $transaction_laundry;
            $mergedTransaction['list_transaction_laundry'] = $list_transactions;

            return new ResponseApiResource(true, 'Transaksi laundry berhasil ditambahkan!', $mergedTransaction, null, 201);
        } catch (ValidationException $error) {
            Log::error('Error validasi: ' . $error->getMessage());
            DB::rollBack();
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('Error saat menambahkan transaksi laundry: ' . $e->getMessage());
            DB::rollBack();
            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan transaksi laundry.', $request->all(), $e->getMessage(), 500);
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
            $transaction_laundry = Transaction_Laundry::withTrashed()->find($id);

            // Periksa apakah Transaksi laundry ditemukan
            if (!$transaction_laundry) {
                Log::info('Transaksi laundry tidak ditemukan dengan id ' . $id);

                return new ResponseApiResource(false, 'Transaksi laundry tidak ditemukan', null, 404);
            }

            // Log info jika Transaksi laundry ditemukan
            Log::info('Detail Transaksi laundry ditemukan', ['id' => $transaction_laundry->id_transaction_laundry, 'name_client_transaction_laundry' => $transaction_laundry->name_client_transaction_laundry]);

            // Return data Transaksi laundry sebagai resource
            return new ResponseApiResource(true, 'Detail Data Transaksi laundry', $transaction_laundry, null, 200);
        } catch (Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Gagal mengambil data Transaksi laundry', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }

    public function getListWithTransactionLaundry($id)
    {
        try {
            // Cari data transaksi laundry berdasarkan ID, termasuk yang sudah dihapus (soft deleted)
            $transaction_laundry = Transaction_Laundry::withTrashed()->with('listTransactionLaundry')->find($id);

            // Periksa apakah transaksi laundry ditemukan
            if (!$transaction_laundry) {
                Log::info('Transaksi laundry dengan ID ' . $id . ' tidak ditemukan.');

                return new ResponseApiResource(false, 'Transaksi laundry tidak ditemukan.', null, 404);
            }

            // Log informasi jika transaksi laundry ditemukan
            Log::info('Detail transaksi laundry ditemukan.', [
                'id' => $transaction_laundry->id_transaction_laundry,
                'nama_client' => $transaction_laundry->name_client_transaction_laundry
            ]);

            // Kembalikan data transaksi laundry sebagai response
            return new ResponseApiResource(true, 'Detail transaksi laundry berhasil ditemukan.', $transaction_laundry, null, 200);
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
            // Cari transaksi laundry berdasarkan ID
            $transaction_laundry = Transaction_Laundry::withTrashed()->find($id);
            if (!$transaction_laundry) {
                return new ResponseApiResource(false, 'Transaksi laundry tidak ditemukan.', [], null, 404);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'id_user_transaction_laundry' => 'required|exists:users,id_user',
                'id_branch_transaction_laundry' => 'required|exists:branches,id_branch',
                'name_client_transaction_laundry' => 'required|string|max:255',
                'status_transaction_laundry' => 'required|in:pending,in_progress,completed,cancelled',
                'notes_transaction_laundry' => 'nullable|string',
                'total_weight_transaction_laundry' => 'required|numeric|min:0',
                'total_price_transaction_laundry' => 'required|numeric|min:0',
                'cash_transaction_laundry' => 'required|numeric|min:0',
                'is_active_transaction_laundry' => 'required|in:active,inactive',
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            $data = [
                'id_user_transaction_laundry' => $request->id_user_transaction_laundry,
                'id_branch_transaction_laundry' => $request->id_branch_transaction_laundry,
                'name_client_transaction_laundry' => $request->name_client_transaction_laundry,
                'status_transaction_laundry' => $request->status_transaction_laundry,
                'notes_transaction_laundry' => $request->notes_transaction_laundry,
                'total_weight_transaction_laundry' => $request->total_weight_transaction_laundry,
                'total_price_transaction_laundry' => $request->total_price_transaction_laundry,
                'cash_transaction_laundry' => $request->cash_transaction_laundry,
                'is_active_transaction_laundry' => $request->is_active_transaction_laundry,
                'first_date_transaction_laundry' =>  $transaction_laundry->first_date_transaction_laundry,
            ];

            // Jika ada input status_transaction_laundry = "completed", update last_date_transaction_laundry
            if ($request->status_transaction_laundry === "completed") {
                // Update last_date_transaction_laundry
                $data['last_date_transaction_laundry'] = Carbon::now();

                // Logging berhasil
                Log::info('Transaksi laundry dengan id ' . $id . ' berhasil diselesaikan.');
            } elseif ($request->status_transaction_laundry === "cancelled") { // Jika ada input status_transaction_laundry = "cancelled", update last_date_transaction_laundry dan non-aktifkan transaksi laundry
                // update last_date_transaction_laundry
                $data['last_date_transaction_laundry'] = Carbon::now();

                // Logging berhasil
                Log::info('Transaksi laundry dengan id ' . $id . ' berhasil dibatalkan.');
            } else {
                // Logging berhasil
                Log::info('Transaksi laundry dengan id ' . $id . ' berhasil diperbarui.');

                $data['is_active_transaction_laundry'] = "active";
                $data['last_date_transaction_laundry'] = null;
            }

            // Update data transaksi laundry
            $transaction_laundry->update($data);

            // Periksa apakah transaksi laundry ingin dinonaktifkan
            if ($transaction_laundry->is_active_transaction_laundry === 'inactive') {
                $transaction_laundry->delete();

                Log::info('Transaksi Laundry berhasil dinonaktifkan', ['id_transaction_laundry' => $id, 'name_client_transaction_laundry' => $transaction_laundry->name_client_transaction_laundry]);
            } elseif ($transaction_laundry->is_active_transaction_laundry === 'active') {
                $transaction_laundry->restore();

                Log::info('Transaksi Laundry berhasil diaktifkan', ['id_transaction_laundry' => $id, 'name_client_transaction_laundry' => $transaction_laundry->name_client_transaction_laundry]);
            }

            // Logging berhasil
            Log::info('Transaksi laundry dengan id ' . $id . ' berhasil diperbarui.');

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Transaksi laundry berhasil diperbarui!', $transaction_laundry, null, 200);
        } catch (ValidationException $error) {
            // Logging error validasi
            Log::error('Error validasi: ' . $error->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            // Logging error umum
            Log::error('Error saat memperbarui transaksi laundry: ' . $e->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan saat memperbarui transaksi laundry.', $request->all(), $e->getMessage(), 500);
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
            $transaction_laundry = Transaction_Laundry::find($id);

            // Jika cabang tidak ditemukan
            if (!$transaction_laundry) {
                Log::info('Cabang tidak ditemukan saat mencoba menghapus', ['id_transaction_laundry' => $id]);

                return new ResponseApiResource(false, 'Cabang tidak ditemukan!', [],  $transaction_laundry, 404);
            }

            // Ubah status is_active_transaction_laundry menjadi 'inactive'
            $transaction_laundry->update(['is_active_transaction_laundry' => 'inactive']);

            // Hapus cabang
            $transaction_laundry->delete();

            // Log informasi perubahan status Cabang
            Log::info('Cabang berhasil dinonaktifkan', ['id_transaction_laundry' => $id, 'name_client_transaction_laundry' => $transaction_laundry->name_client_transaction_laundry]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Cabang berhasil dinonaktifkan!', $transaction_laundry, 200);
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
            $transaction_laundry = Transaction_Laundry::withTrashed()->find($id);

            // Jika Transaksi Laundry tidak ditemukan
            if (!$transaction_laundry) {
                Log::info('Transaksi Laundry tidak ditemukan saat mencoba dipulihkan', ['id_transaction_laundry' => $id]);

                return new ResponseApiResource(false, 'Transaksi Laundry tidak ditemukan!', [],  $transaction_laundry, 404);
            }

            // Ubah status is_active_transaction_laundry menjadi 'active'
            $transaction_laundry->update(['is_active_transaction_laundry' => 'active']);

            // Pulihkan Transaksi Laundry
            $transaction_laundry->restore();

            // Log informasi perubahan status Transaksi Laundry
            Log::info('Transaksi Laundry berhasil dipulihkan', ['id_transaction_laundry' => $id, 'name_client_transaction_laundry' => $transaction_laundry->name_client_transaction_laundry]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Transaksi Laundry berhasil dipulihkan!', $transaction_laundry, 200);
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
            $transaction_laundry = Transaction_Laundry::withTrashed()->find($id);

            // Jika Transaksi Laundry tidak ditemukan
            if (!$transaction_laundry) {
                Log::info('Transaksi Laundry tidak ditemukan saat mencoba hapus permanent', ['id_transaction_laundry' => $id]);

                return new ResponseApiResource(false, 'Transaksi Laundry tidak ditemukan!', $transaction_laundry,  $transaction_laundry, 404);
            }

            // Ubah status is_active_transaction_laundry menjadi 'inactive'
            $transaction_laundry->update(['is_active_transaction_laundry' => 'inactive']);

            // Hapus Transaksi Laundry permanent
            $transaction_laundry->forceDelete();

            // Log informasi perubahan status Transaksi Laundry
            Log::info('Transaksi Laundry berhasil hapus permanent', ['id_transaction_laundry' => $id, 'name_client_transaction_laundry' => $transaction_laundry->name_client_transaction_laundry]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Transaksi Laundry berhasil hapus permanent!', $transaction_laundry, 200);
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
