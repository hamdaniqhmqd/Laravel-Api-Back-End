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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ListTransactionLaundryController extends Controller
{
    /**
     * index
     *
     * @return void
     */

    public function index()
    {
        try {
            // Dapatkan semua data kecuali yang memiliki is_active_list_transaction_laundry = 'inactive'
            $list_transaction_laundry = List_Transaction_Laundry::where('is_active_list_transaction_laundry', '!=', 'inactive')->latest()->get();

            Log::info('Sukses menampilkan data list transaksi laundry yang aktif');

            // Kembalikan data list transaksi laundry sebagai resource
            return new ResponseApiResource(true, 'Daftar Data list transaksi laundry Aktif', $list_transaction_laundry, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data list transaksi laundry Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data list transaksi laundry Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data list transaksi laundry Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            $list_transaction_laundry =  List_Transaction_Laundry::withTrashed()->latest()->get();

            Log::info('Sukses menampilkan data list transaksi laundry');

            // Kembalikan data list transaksi laundry sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data list transaksi laundry', $list_transaction_laundry, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data list transaksi laundry Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data list transaksi laundry Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data list transaksi laundry Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            $list_transaction_laundry =  List_Transaction_Laundry::onlyTrashed()->where('is_active_list_transaction_laundry', '!=', 'active')->latest()->get();

            Log::info('Sukses menampilkan data list transaksi laundry yang dihapus');

            // Kembalikan data list transaksi laundry sebagai resource
            return new ResponseApiResource(true, 'Daftar data list transaksi laundry yang dihapus', $list_transaction_laundry, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data list transaksi laundry Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data list transaksi laundry Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data list transaksi laundry Tidak Ditemukan!', null, $error->getMessage(), 404);
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
                'id_transaction_laundry' => 'required|exists:transaction_laundries,id_transaction_laundry',
                'id_item_laundry' => 'required|exists:laundry_items,id_laundry_item',
                // 'price_list_transaction_laundry' => 'required|numeric|min:0',
                'weight_list_transaction_laundry' => 'required|numeric|min:0',
                // 'total_price_list_transaction_laundry' => 'required|integer|min:1',
                // 'status_list_transaction_laundry' => 'required|in:pending,completed,cancelled',
                'note_list_transaction_laundry' => 'nullable|string',
                'cash_transaction_laundry' => 'nullable|numeric|min:0',
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Cari Item Laundry berdasarkan ID transaksi laundry
            $laundry_item = Laundry_Item::withTrashed()->findOrFail($request->id_item_laundry);
            $price_laundry_item = $laundry_item->price_laundry_item;

            // Rumus
            $total_price = $price_laundry_item * $request->weight_list_transaction_laundry;

            // Jika berhasil, ubah data transaksi laundry
            $transaction_laundry = Transaction_Laundry::withTrashed()->find($request->id_transaction_laundry);
            $count = $transaction_laundry->count_item_laundry_transaction_laundry + 1;

            $weight_transaction_laundry = $transaction_laundry->total_weight_transaction_laundry + $request->weight_list_transaction_laundry;
            $subtotal_transaction_laundry = $transaction_laundry->total_price_transaction_laundry + $total_price;

            $total_transaction_laundry = ($subtotal_transaction_laundry - $transaction_laundry->promo_transaction_laundry) + $transaction_laundry->additional_cost_transaction_laundry;

            $total_transaction = $total_transaction_laundry;
            $cash_transaction_laundry = $transaction_laundry->cash_transaction_laundry + $request->cash_transaction_laundry;

            $change_money = $cash_transaction_laundry - $total_transaction;

            if ($change_money < 0) {
                Log::info('Uang pembayaran kurang!', ['cash_transaction_laundry' => $cash_transaction_laundry, 'total_transaction' => $total_transaction]);
                return new ResponseApiResource(false, 'Uang pembayaran kurang!', $request->all(), ['cash_transaction_laundry' => 'Uang tidak mencukupi untuk membayar total transaksi'], 400);
            }

            // Membuat list transaksi laundry baru
            $list_transaction_laundry = List_Transaction_Laundry::create([
                'id_transaction_laundry' => $request->id_transaction_laundry,
                'id_item_laundry' => $request->id_item_laundry,
                'price_list_transaction_laundry' => $price_laundry_item,
                'weight_list_transaction_laundry' => $request->weight_list_transaction_laundry,
                'total_price_list_transaction_laundry' => $total_price,
                'status_list_transaction_laundry' => 'pending',
                'note_list_transaction_laundry' => $request->note_list_transaction_laundry,
                'is_active_list_transaction_laundry' => 'active',
            ]);

            // Update total transaksi laundry
            $transaction_laundry->update([
                'total_weight_transaction_laundry' => $weight_transaction_laundry,
                'total_price_transaction_laundry' => $subtotal_transaction_laundry,
                'count_item_laundry_transaction_laundry' => $count,
                'total_transaction_laundry' => $total_transaction,
                'cash_transaction_laundry' => $cash_transaction_laundry,
                'change_money_transaction_laundry' => $change_money,
            ]);

            // Kembalikan response sukses
            Log::info('List transaksi laundry berhasil ditambahkan dengan id ' . $list_transaction_laundry->id_list_transaction_laundry);

            return new ResponseApiResource(true, 'List transaksi laundry berhasil ditambahkan!', $list_transaction_laundry, null, 201);
        } catch (ValidationException $error) {
            // Logging error untuk debugging
            Log::error('Error validasi: ' . $error->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            // Logging error untuk debugging
            Log::error('Error saat menambahkan list transaksi laundry: ' . $e->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan list transaksi laundry.', $request->all(), $e->getMessage(), 500);
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
            $list_transaction_laundry = List_Transaction_Laundry::withTrashed()->find($id);

            // Periksa apakah List Transaksi laundry ditemukan
            if (!$list_transaction_laundry) {
                Log::info('List Transaksi laundry tidak ditemukan dengan id ' . $id);

                return new ResponseApiResource(false, 'List Transaksi laundry tidak ditemukan', null, 404);
            }

            // Log info jika List Transaksi laundry ditemukan
            Log::info('Detail List Transaksi laundry ditemukan', ['id' => $list_transaction_laundry->id_list_transaction_laundry, 'id_transaction_laundry' => $list_transaction_laundry->id_transaction_laundry]);

            // Return data List Transaksi laundry sebagai resource
            return new ResponseApiResource(true, 'Detail Data List Transaksi laundry : ', $list_transaction_laundry, null, 200);
        } catch (Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Gagal mengambil data List Transaksi laundry', [
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
            // Cari list transaksi laundry berdasarkan ID (termasuk yang soft deleted)
            $list_transaction_laundry = List_Transaction_Laundry::withTrashed()->find($id);
            if (!$list_transaction_laundry) {
                return new ResponseApiResource(false, 'List transaksi laundry tidak ditemukan.', $request->all(), null, 404);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'id_transaction_laundry' => 'required|exists:transaction_laundries,id_transaction_laundry',
                'id_item_laundry' => 'required|exists:laundry_items,id_laundry_item',
                'weight_list_transaction_laundry' => 'required|numeric|min:0',
                'status_list_transaction_laundry' => 'required|in:pending,completed,cancelled',
                'note_list_transaction_laundry' => 'nullable|string',
                'is_active_list_transaction_laundry' => 'required|in:active,inactive',
                'cash_transaction_laundry' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Ambil data transaction_laundry
            $transaction_laundry = Transaction_Laundry::withTrashed()->findOrFail($request->id_transaction_laundry);
            if (!$transaction_laundry) {
                return new ResponseApiResource(false, 'Transaction laundry tidak ditemukan.', [], null, 404);
            }

            // Ambil data laundry item
            $laundry_item = Laundry_Item::withTrashed()->find($request->id_item_laundry);
            $price_laundry_item = $laundry_item->price_laundry_item;

            // --- Perhitungan ulang data transaksi laundry ---

            // Berat sebelumnya dikurangi berat list yang lama
            $current_total_weight = $transaction_laundry->total_weight_transaction_laundry - $list_transaction_laundry->weight_list_transaction_laundry;
            if ($current_total_weight <= 0) {
                $current_total_weight = 0;
            }

            // Harga sebelumnya dikurangi total harga list yang lama
            $current_total_price = $transaction_laundry->total_price_transaction_laundry - $list_transaction_laundry->total_price_list_transaction_laundry;

            // Tambahkan berat dan harga baru
            $new_total_weight = $current_total_weight + $request->weight_list_transaction_laundry;
            $new_total_price = $current_total_price + ($price_laundry_item * $request->weight_list_transaction_laundry);

            // Hitung total transaksi setelah promo dan biaya tambahan
            $new_total_transaction = ($new_total_price - $transaction_laundry->promo_transaction_laundry) + $transaction_laundry->additional_cost_transaction_laundry;

            // Jika user input tambahan cash saat update, tambahkan ke cash lama
            $new_cash = $transaction_laundry->cash_transaction_laundry;
            if ($request->filled('cash_transaction_laundry')) {
                $new_cash += $request->cash_transaction_laundry;
            }

            $new_change_money = $new_cash - $new_total_transaction;

            if ($new_change_money < 0) {
                Log::info('Uang pembayaran kurang!', ['cash_transaction_laundry' => $new_cash, 'total_transaction' => $new_total_transaction]);
                return new ResponseApiResource(false, 'Uang pembayaran kurang!', $request->all(), ['cash_transaction_laundry' => 'Uang tidak mencukupi untuk membayar total transaksi'], 400);
            }

            // --- Update list transaksi laundry ---
            $list_transaction_laundry->update([
                'id_transaction_laundry' => $request->id_transaction_laundry,
                'id_item_laundry' => $request->id_item_laundry,
                'price_list_transaction_laundry' => $price_laundry_item,
                'weight_list_transaction_laundry' => $request->weight_list_transaction_laundry,
                'total_price_list_transaction_laundry' => $price_laundry_item * $request->weight_list_transaction_laundry,
                'status_list_transaction_laundry' => $request->status_list_transaction_laundry,
                'note_list_transaction_laundry' => $request->note_list_transaction_laundry,
                'is_active_list_transaction_laundry' => $request->is_active_list_transaction_laundry,
            ]);

            // Handle soft delete / restore berdasarkan is_active
            if ($request->is_active_list_transaction_laundry == 'inactive' && !$list_transaction_laundry->trashed()) {
                $list_transaction_laundry->delete();
                Log::info('List transaksi laundry berhasil dinonaktifkan.', ['id_list_transaction_laundry' => $id]);
            } elseif ($request->is_active_list_transaction_laundry == 'active' && $list_transaction_laundry->trashed()) {
                $list_transaction_laundry->restore();
                Log::info('List transaksi laundry berhasil diaktifkan.', ['id_list_transaction_laundry' => $id]);
            }

            // --- Update transaksi laundry utama ---
            $transaction_laundry->update([
                'total_weight_transaction_laundry' => $new_total_weight,
                'total_price_transaction_laundry' => $new_total_price,
                'total_transaction_laundry' => $new_total_transaction,
                'cash_transaction_laundry' => $new_cash,
                'change_money_transaction_laundry' => $new_change_money,
            ]);

            Log::info('List transaksi laundry dengan id ' . $id . ' berhasil diperbarui.');

            return new ResponseApiResource(true, 'List transaksi laundry berhasil diperbarui!', [$list_transaction_laundry, $transaction_laundry], null, 200);
        } catch (ValidationException $error) {
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            Log::error('Error saat memperbarui list transaksi laundry: ' . $e->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan saat memperbarui list transaksi laundry.', $request->all(), $e->getMessage(), 500);
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
            // Cari List transaksi laundry berdasarkan ID
            $list_transaction_laundry = List_Transaction_Laundry::find($id);

            // Jika List transaksi laundry tidak ditemukan
            if (!$list_transaction_laundry) {
                Log::info('List transaksi laundry tidak ditemukan saat mencoba menghapus', ['id_transaction_laundry' => $id]);

                return new ResponseApiResource(false, 'List transaksi laundry tidak ditemukan!', [],  $list_transaction_laundry, 404);
            }

            // Ubah status is_active_list_transaction_laundry menjadi 'inactive'
            $list_transaction_laundry->update(['is_active_list_transaction_laundry' => 'inactive']);

            // Hapus List transaksi laundry
            $list_transaction_laundry->delete();

            // Log informasi perubahan status List transaksi laundry
            Log::info('List transaksi laundry berhasil dinonaktifkan', ['id_transaction_laundry' => $id, 'id_transaction_laundry' => $list_transaction_laundry->id_transaction_laundry]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'List transaksi laundry berhasil dinonaktifkan!', $list_transaction_laundry, 200);
        } catch (Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal menonaktifkan List transaksi laundry', [
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
            $list_transaction_laundry = List_Transaction_Laundry::withTrashed()->find($id);

            // Jika Transaksi Laundry tidak ditemukan
            if (!$list_transaction_laundry) {
                Log::info('Transaksi Laundry tidak ditemukan saat mencoba dipulihkan', ['id_transaction_laundry' => $id]);

                return new ResponseApiResource(false, 'Transaksi Laundry tidak ditemukan!', [],  $list_transaction_laundry, 404);
            }

            // Ubah status is_active_list_transaction_laundry menjadi 'active'
            $list_transaction_laundry->update(['is_active_list_transaction_laundry' => 'active']);

            // Pulihkan Transaksi Laundry
            $list_transaction_laundry->restore();

            // Log informasi perubahan status Transaksi Laundry
            Log::info('Transaksi Laundry berhasil dipulihkan', ['id_transaction_laundry' => $id, 'id_transaction_laundry' => $list_transaction_laundry->id_transaction_laundry]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Transaksi Laundry berhasil dipulihkan!', $list_transaction_laundry, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal memulihkan Transaksi Laundry', [
                'id_branch' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }

    public function forceDestroy($id)
    {
        try {
            // Cari Transaksi Laundry berdasarkan ID
            $list_transaction_laundry = List_Transaction_Laundry::withTrashed()->find($id);

            // Jika Transaksi Laundry tidak ditemukan
            if (!$list_transaction_laundry) {
                Log::info('Transaksi Laundry tidak ditemukan saat mencoba hapus permanent', ['id_transaction_laundry' => $id]);

                return new ResponseApiResource(false, 'Transaksi Laundry tidak ditemukan!', [],  $list_transaction_laundry, 404);
            }

            // Ubah status is_active_list_transaction_laundry menjadi 'inactive'
            $list_transaction_laundry->update(['is_active_list_transaction_laundry' => 'inactive']);

            // Hapus Transaksi Laundry permanent
            $list_transaction_laundry->forceDelete();

            // Log informasi perubahan status Transaksi Laundry
            Log::info('Transaksi Laundry berhasil hapus permanent', ['id_transaction_laundry' => $id, 'id_transaction_laundry' => $list_transaction_laundry->id_transaction_laundry]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Transaksi Laundry berhasil hapus permanent!', $list_transaction_laundry, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal hapus permanent Transaksi Laundry', [
                'id_branch' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }
}
