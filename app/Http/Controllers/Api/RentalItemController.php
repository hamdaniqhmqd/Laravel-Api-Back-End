<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ResponseApiResource;
use App\Models\Logging;
use App\Models\Rental_Item;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RentalItemController extends Controller
{
    /**
     * index
     *
     * @return void
     */

    public function index()
    {
        try {
            // Dapatkan semua Rental Item kecuali yang memiliki is_active_rental_item = 'inactive'
            $rental_items = Rental_Item::where('is_active_rental_item', '!=', 'inactive')->latest()->get();

            Log::info('Sukses menampilkan data Rental Item yang aktif');

            // Kembalikan data Rental Item sebagai resource
            return new ResponseApiResource(true, 'Daftar Data Rental Item Aktif', $rental_items, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data Rental Item Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data Rental Item Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data Rental Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            // Mengambil semua Rental Item
            $rental_item =  Rental_Item::withTrashed()->latest()->get();

            Log::info('Sukses menampilkan data Rental Item');

            // Kembalikan data Rental Item sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data Rental Item', $rental_item, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data Rental Item Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data Rental Item Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data Rental Item Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            // Mengambil semua Rental Item
            $rental_item =  Rental_Item::onlyTrashed()->where('is_active_rental_item', '!=', 'active')->latest()->get();

            Log::info('Sukses menampilkan data Rental Item');

            // Kembalikan data Rental Item sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data Rental Item', $rental_item, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data Rental Item Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data Rental Item Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data Rental Item Tidak Ditemukan!', null, $error->getMessage(), 404);
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
                'number_rental_item' => 'required|string|max:255|unique:rental_items,number_rental_item',
                'name_rental_item' => 'required|string|max:255',
                'price_rental_item' => 'required|integer|min:0',
                'status_rental_item' => 'required|in:available,rented,maintenance',
                'condition_rental_item' => 'required|in:clean,dirty,damaged',
                'description_rental_item' => 'nullable|string',
                'is_active_rental_item' => 'required|in:active,inactive',
                'id_branch_rental_item' => 'required|exists:branches,id_branch'
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Membuat rental item baru
            $rentalItem = Rental_Item::create([
                'number_rental_item' => $request->number_rental_item,
                'name_rental_item' => $request->name_rental_item,
                'price_rental_item' => $request->price_rental_item,
                'status_rental_item' => $request->status_rental_item ?? 'available',
                'condition_rental_item' => $request->condition_rental_item ?? 'clean',
                'description_rental_item' => $request->description_rental_item,
                'is_active_rental_item' => $request->is_active_rental_item ?? 'active',
                'id_branch_rental_item' => $request->id_branch_rental_item,
            ]);

            // Logging sukses
            Log::info('Rental item berhasil ditambahkan dengan id_rental_item ' . $rentalItem->id_rental_item);

            return new ResponseApiResource(true, 'Rental item berhasil ditambahkan!', $rentalItem, null, 201);
        } catch (ValidationException $error) {
            // Logging error validasi
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            // Logging error umum
            Log::error('Error saat menambahkan rental item: ' . $e->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal Menambah Data rental item: " . $e->getMessage()
            );

            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan rental item.', $request->all(), $e->getMessage(), 500);
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
            // Cari client berdasarkan ID
            $rental_item = Rental_Item::withTrashed()->find($id);

            // Periksa apakah client ditemukan
            if (!$rental_item) {
                Log::info('Client tidak ditemukan dengan id ' . $id);

                return new ResponseApiResource(false, 'client tidak ditemukan', null, 404);
            }

            // Log info jika client ditemukan
            Log::info('Detail client ditemukan', ['id' => $rental_item->id_rental_item, 'nama' => $rental_item->name_rental_item]);

            // Return data client sebagai resource
            return new ResponseApiResource(true, 'Detail Data client!', $rental_item, null, 200);
        } catch (Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Gagal mengambil data client', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal mengambil Data client: " . $e->getMessage()
            );

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
            // Cari rental item berdasarkan ID
            $rentalItem = Rental_Item::withTrashed()->latest()->find($id);
            if (!$rentalItem) {
                return new ResponseApiResource(false, 'Rental item tidak ditemukan.', [], null, 404);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'number_rental_item' => 'required|string|max:255|unique:rental_items,number_rental_item,' . $id . ',id_rental_item',
                'name_rental_item' => 'required|string|max:255',
                'price_rental_item' => 'required|integer|min:0',
                'status_rental_item' => 'required|in:available,rented,maintenance',
                'condition_rental_item' => 'required|in:clean,dirty,damaged',
                'description_rental_item' => 'nullable|string',
                'is_active_rental_item' => 'required|in:active,inactive',
                'id_branch_rental_item' => 'required|exists:branches,id_branch'
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            if ($request->is_active_rental_item === 'inactive') {
                $rentalItem->delete();

                Log::info('Rental item berhasil dinonaktifkan', ['id_rental_item' => $id, 'name' => $rentalItem->name_rental_item]);

                return new ResponseApiResource(true, 'Rental item berhasil dinonaktifkan!', $rentalItem, null, 200);
            }

            // Update data rental item
            $rentalItem->update([
                'number_rental_item' => $request->number_rental_item,
                'name_rental_item' => $request->name_rental_item,
                'price_rental_item' => $request->price_rental_item,
                'status_rental_item' => $request->status_rental_item,
                'condition_rental_item' => $request->condition_rental_item,
                'description_rental_item' => $request->description_rental_item,
                'is_active_rental_item' => $request->is_active_rental_item,
                'id_branch_rental_item' => $request->id_branch_rental_item,
            ]);

            // Logging sukses
            Log::info('Rental item dengan id_rental_item ' . $id . ' berhasil diperbarui.');

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Rental item berhasil diperbarui!', $rentalItem, null, 200);
        } catch (ValidationException $error) {
            // Logging error validasi
            Log::error('Error validasi: ' . $error->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            // Logging error umum
            Log::error('Error saat memperbarui rental item: ' . $e->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal Memperbarui Data rental item: " . $e->getMessage()
            );

            return new ResponseApiResource(false, 'Terjadi kesalahan saat memperbarui rental item.', $request->all(), $e->getMessage(), 500);
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
            // Cari Client berdasarkan ID
            $rental_item = Rental_Item::withTrashed()->find($id);

            // Jika Client tidak ditemukan
            if (!$rental_item) {
                Log::info('Client tidak ditemukan saat mencoba menghapus', ['id_rental_item' => $id]);

                return new ResponseApiResource(false, 'Client tidak ditemukan!', $id,  $rental_item, 404);
            }

            // Ubah status is_active_rental_item menjadi 'inactive'
            $rental_item->update(['is_active_rental_item' => 'inactive']);

            // Hapus
            $rental_item->delete();

            // Log informasi perubahan status Client
            Log::info('Client berhasil dinonaktifkan', ['id_rental_item' => $id, 'name_rental_item' => $rental_item->name_rental_item]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Client berhasil dinonaktifkan!', $rental_item, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal menonaktifkan Client', [
                'id_rental_item' => $id,
                'error'   => $e->getMessage()
            ]);

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal menonaktifkan Client: " . $e->getMessage()
            );

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }

    public function restore($id)
    {
        try {
            // Cari Client berdasarkan ID
            $rental_item = Rental_Item::withTrashed()->find($id);

            // Jika Client tidak ditemukan
            if (!$rental_item) {
                Log::info('Client tidak ditemukan saat mencoba dipulihkan', ['id_rental_item' => $id]);

                return new ResponseApiResource(false, 'Client tidak ditemukan!', $id,  $rental_item, 404);
            }

            // Ubah status is_active_rental_item menjadi 'active'
            $rental_item->update(['is_active_rental_item' => 'active']);

            // Pulihkan
            $rental_item->restore();

            // Log informasi perubahan status Client
            Log::info('Client berhasil dipulihkan', ['id_rental_item' => $id, 'name_rental_item' => $rental_item->name_rental_item]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Client berhasil dipulihkan!', $rental_item, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal memulihkan Client', [
                'id_rental_item' => $id,
                'error'   => $e->getMessage()
            ]);

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal memulihkan Client: " . $e->getMessage()
            );

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }

    public function forceDestroy($id)
    {
        try {
            // Cari Client berdasarkan ID
            $rental_item = Rental_Item::withTrashed()->find($id);

            // Jika Client tidak ditemukan
            if (!$rental_item) {
                Log::info('Client tidak ditemukan saat mencoba menghapus permanent', ['id_rental_item' => $id]);

                return new ResponseApiResource(false, 'Client tidak ditemukan!', $id,  $rental_item, 404);
            }

            // Ubah status is_active_rental_item menjadi 'inactive'
            $rental_item->update(['is_active_rental_item' => 'inactive']);

            // Hapus permanent
            $rental_item->forceDelete();

            // Log informasi perubahan status Client
            Log::info('Client berhasil dihapus permanen', ['id_rental_item' => $id, 'name_rental_item' => $rental_item->name_rental_item]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Client berhasil dihapus permanen!', $rental_item, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal menghapus permanen Client', [
                'id_rental_item' => $id,
                'error'   => $e->getMessage()
            ]);

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal menghapus permanen Client: " . $e->getMessage()
            );

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', [], $e->getMessage(), 500);
        }
    }
}
