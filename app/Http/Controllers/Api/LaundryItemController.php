<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Laundry_Item;
use App\Http\Resources\ResponseApiResource;
use App\Models\Logging;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LaundryItemController extends Controller
{
    /**
     * index
     *
     * @return void
     */

    public function index()
    {
        try {
            // Dapatkan semua Laundry Item kecuali yang memiliki is_active_laundry_item = 'inactive'
            $laundry_items = Laundry_Item::where('is_active_laundry_item', '!=', 'inactive')->latest()->get();

            Log::info('Sukses menampilkan data Laundry Item yang aktif');

            // Kembalikan data Laundry Item sebagai resource
            return new ResponseApiResource(true, 'Daftar Data Laundry Item Aktif', $laundry_items, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data Laundry Item Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data Laundry Item Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data Laundry Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            // Mengambil semua Laundry Item
            $laundry_items =  Laundry_Item::withTrashed()->latest()->get();

            Log::info('Sukses menampilkan seluruh data Laundry Item');

            // Kembalikan data Laundry Item sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data Laundry Item', $laundry_items, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data Laundry Item Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data Laundry Item Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data Laundry Item Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            // Mengambil semua Laundry Item
            $laundry_items =  Laundry_Item::onlyTrashed()->where('is_active_laundry_item', '!=', 'active')->latest()->get();

            Log::info('Sukses menampilkan data Laundry Item yang dihapus');

            // Kembalikan data Laundry Item sebagai resource
            return new ResponseApiResource(true, 'Daftar Data Laundry Item yang dihapus', $laundry_items, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data Laundry Item Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data Laundry Item Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data Laundry Item Tidak Ditemukan!', null, $error->getMessage(), 404);
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
                'name_laundry_item' => 'required|string|max:255',
                'price_laundry_item' => 'required|integer',
                'time_laundry_item' => 'nullable|date_format:H:i:s',
                'description_laundry_item' => 'nullable|string',
                'is_active_laundry_item' => 'required|in:active,inactive',
                'id_branch_laundry_item' => 'nullable|exists:branches,id_branch'
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Membuat laundry item baru
            $laundryItem = Laundry_Item::create([
                'name_laundry_item' => $request->name_laundry_item,
                'price_laundry_item' => $request->price_laundry_item,
                'time_laundry_item' => $request->time_laundry_item,
                'description_laundry_item' => $request->description_laundry_item,
                'is_active_laundry_item' => $request->is_active_laundry_item,
                'id_branch_laundry_item' => $request->id_branch_laundry_item,
            ]);

            // Logging sukses
            Log::info('Laundry item berhasil ditambahkan dengan id_laundry_item ' . $laundryItem->id_laundry_item);

            return new ResponseApiResource(true, 'Laundry item berhasil ditambahkan!', $laundryItem, null, 201);
        } catch (ValidationException $error) {
            // Logging error validasi
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            // Logging error umum
            Log::error('Error saat menambahkan laundry item: ' . $e->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal Menambah Data laundry item: " . $e->getMessage()
            );

            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan laundry item.', $request->all(), $e->getMessage(), 500);
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
            $laundry_item = Laundry_Item::withTrashed()->find($id);

            // Periksa apakah client ditemukan
            if (!$laundry_item) {
                Log::info('Client tidak ditemukan dengan id ' . $id);

                return new ResponseApiResource(false, 'client tidak ditemukan', null, 404);
            }

            // Log info jika client ditemukan
            Log::info('Detail client ditemukan', ['id' => $laundry_item->id_laundry_item, 'nama' => $laundry_item->name_laundry_item]);

            // Return data client sebagai resource
            return new ResponseApiResource(true, 'Detail Data client!', $laundry_item, null, 200);
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
            // Cari laundry item berdasarkan ID
            $laundryItem = Laundry_Item::withTrashed()->find($id);
            if (!$laundryItem) {
                Log::info('Laundry item tidak ditemukan: ', ['id_laundry_item' => $id]);

                return new ResponseApiResource(false, 'Laundry item tidak ditemukan.', [], null, 404);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'name_laundry_item' => 'required|string|max:255',
                'price_laundry_item' => 'required|integer',
                'time_laundry_item' => 'nullable|date_format:H:i:s',
                'description_laundry_item' => 'nullable|string',
                'is_active_laundry_item' => 'required|in:active,inactive',
                'id_branch_laundry_item' => 'nullable|exists:branches,id_branch'
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            if ($request->is_active_laundry_item === 'inactive') {
                $laundryItem->delete();

                Log::info('Laundry item berhasil dinonaktifkan', ['id_laundry_item' => $id, 'name' => $laundryItem->name_laundry_item]);

                return new ResponseApiResource(true, 'Laundry item berhasil dinonaktifkan!', $laundryItem, null, 200);
            }

            // Update data laundry item
            $laundryItem->update([
                'name_laundry_item' => $request->name_laundry_item,
                'price_laundry_item' => $request->price_laundry_item,
                'time_laundry_item' => $request->time_laundry_item,
                'description_laundry_item' => $request->description_laundry_item,
                'is_active_laundry_item' => $request->is_active_laundry_item,
                'id_branch_laundry_item' => $request->id_branch_laundry_item,
            ]);

            // Logging berhasil
            Log::info('Laundry item dengan id_laundry_item ' . $id . ' berhasil diperbarui.');

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Laundry item berhasil diperbarui!', $laundryItem, null, 200);
        } catch (ValidationException $error) {
            // Logging error validasi
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            // Logging error umum
            Log::error('Error saat memperbarui laundry item: ' . $e->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan saat memperbarui laundry item.', $request->all(), $e->getMessage(), 500);
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
            $laundryItem = Laundry_Item::withTrashed()->find($id);

            // Jika Client tidak ditemukan
            if (!$laundryItem) {
                Log::info('Client tidak ditemukan saat mencoba menghapus', ['id_laundry_item' => $id]);

                return new ResponseApiResource(false, 'Client tidak ditemukan!', $id,  $laundryItem, 404);
            }

            // Ubah status is_active_laundry_item menjadi 'inactive'
            $laundryItem->update(['is_active_laundry_item' => 'inactive']);

            // hapus
            $laundryItem->delete();

            // Log informasi perubahan status Client
            Log::info('Client berhasil dinonaktifkan', ['id_laundry_item' => $id, 'name_laundry_item' => $laundryItem->name_laundry_item]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Client berhasil dinonaktifkan!', $laundryItem, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal menonaktifkan Client', [
                'id_laundry_item' => $id,
                'error'   => $e->getMessage()
            ]);

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal menonaktifkan Client: " . $e->getMessage()
            );

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }

    public function restore($id)
    {
        try {
            // Cari Client berdasarkan ID
            $laundryItem = Laundry_Item::withTrashed()->find($id);

            // Jika Client tidak ditemukan
            if (!$laundryItem) {
                Log::info('Client tidak ditemukan saat mencoba dipulihkan', ['id_laundry_item' => $id]);

                return new ResponseApiResource(false, 'Client tidak ditemukan!', $id,  $laundryItem, 404);
            }

            // Ubah status is_active_laundry_item menjadi 'active'
            $laundryItem->update(['is_active_laundry_item' => 'active']);

            // hapus
            $laundryItem->restore();

            // Log informasi perubahan status Client
            Log::info('Client berhasil dipulihkan', ['id_laundry_item' => $id, 'name_laundry_item' => $laundryItem->name_laundry_item]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Client berhasil dipulihkan!', $laundryItem, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal memulihkan Client', [
                'id_laundry_item' => $id,
                'error'   => $e->getMessage()
            ]);

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal memulihkan Client: " . $e->getMessage()
            );

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }

    public function forceDestroy($id)
    {
        try {
            // Cari Client berdasarkan ID
            $laundryItem = Laundry_Item::withTrashed()->find($id);

            // Jika Client tidak ditemukan
            if (!$laundryItem) {
                Log::info('Client tidak ditemukan saat mencoba menghapus permanent', ['id_laundry_item' => $id]);

                return new ResponseApiResource(false, 'Client tidak ditemukan!', $id,  $laundryItem, 404);
            }

            // Ubah status is_active_laundry_item menjadi 'inactive'
            $laundryItem->update(['is_active_laundry_item' => 'inactive']);

            // hapus
            $laundryItem->forceDelete();

            // Log informasi perubahan status Client
            Log::info('Client berhasil menghapus permanent', ['id_laundry_item' => $id, 'name_laundry_item' => $laundryItem->name_laundry_item]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Client berhasil menghapus permanent!', $laundryItem, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal menghapus permanent Client', [
                'id_laundry_item' => $id,
                'error'   => $e->getMessage()
            ]);

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal menghapus permanent Client: " . $e->getMessage()
            );

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }
}
