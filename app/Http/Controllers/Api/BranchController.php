<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\Branch;
use App\Models\Logging;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BranchController extends Controller
{
    /**
     * index
     *
     * @return void
     */

    public function index()
    {
        try {
            // Dapatkan semua cabang kecuali yang memiliki is_active_branch = 'inactive'
            $branches = Branch::where('is_active_branch', '!=', 'inactive')->latest()->get();

            Log::info('Sukses menampilkan data cabang yang aktif');

            // Kembalikan data cabang sebagai resource
            return new ResponseApiResource(true, 'Daftar Data Cabang Aktif', $branches, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data Cabang Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data Cabang Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data Cabang Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            // Mengambil semua cabang
            $branches =  Branch::withTrashed()->latest()->get();

            Log::info('Sukses menampilkan data cabang');

            // Kembalikan data cabang sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data cabang', $branches, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data cabang Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data cabang Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data cabang Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            // Mengambil semua cabang
            $branches =  Branch::onlyTrashed()->where('is_active_branch', '!=', 'active')->latest()->get();

            Log::info('Sukses menampilkan data cabang yang dihapus');

            // Kembalikan data cabang sebagai resource
            return new ResponseApiResource(true, 'Daftar data cabang yang dihapus', $branches, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data cabang Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data cabang Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data cabang Tidak Ditemukan!', null, $error->getMessage(), 404);
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
                'name_branch' => 'required|string|max:255',
                'city_branch' => 'required|string|max:255',
                'address_branch' => 'required|string',
                'is_active_branch' => 'required|in:active,inactive'
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Membuat branch baru
            $branch = Branch::create([
                'name_branch' => $request->name_branch,
                'city_branch' => $request->city_branch,
                'address_branch' => $request->address_branch,
                'is_active_branch' => $request->is_active_branch,
            ]);

            // Kembalikan response sukses
            Log::info('Branch berhasil ditambahkan dengan id_branch ' . $branch->id_branch);

            return new ResponseApiResource(true, 'Branch berhasil ditambahkan!', $branch, null, 201);
        } catch (ValidationException $error) {
            // Logging error untuk debugging
            Log::error('Error validasi: ' . $error->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            // Logging error untuk debugging
            Log::error('Error saat menambahkan branch: ' . $e->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan branch.', $request->all(), $e->getMessage(), 500);
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
            // Cari cabang berdasarkan ID
            $branch = Branch::withTrashed()->find($id);

            // Periksa apakah cabang ditemukan
            if (!$branch) {
                Log::info('Cabang tidak ditemukan dengan id ' . $id);

                return new ResponseApiResource(false, 'Cabang tidak ditemukan', null, 404);
            }

            // Log info jika cabang ditemukan
            Log::info('Detail cabang ditemukan', ['id' => $branch->id_branch, 'nama' => $branch->name_branch]);

            // Return data cabang sebagai resource
            return new ResponseApiResource(true, 'Detail Data cabang!', $branch, null, 200);
        } catch (Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Gagal mengambil data cabang', [
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
            // Cari branch berdasarkan ID
            $branch = Branch::withTrashed()->find($id);
            if (!$branch) {
                return new ResponseApiResource(false, 'Branch tidak ditemukan.', [], null, 404);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'name_branch' => 'required|string|max:255',
                'city_branch' => 'required|string|max:255',
                'address_branch' => 'required|string',
                'is_active_branch' => 'required|in:active,inactive'
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Update data branch
            $branch->update([
                'name_branch' => $request->name_branch,
                'city_branch' => $request->city_branch,
                'address_branch' => $request->address_branch,
                'is_active_branch' => $request->is_active_branch,
            ]);

            // Logging berhasil
            Log::info('Branch dengan id_branch ' . $id . ' berhasil diperbarui.');

            if ($request->is_active_branch === 'inactive') {
                $branch->delete();

                Log::info('Branch berhasil dinonaktifkan', ['id_user' => $id, 'name_branch' => $branch->name_branch]);
            } elseif ($request->is_active_branch === 'active') {
                $branch->restore();

                Log::info('Branch berhasil diaktifkan', ['id_user' => $id, 'name_branch' => $branch->name_branch]);
            }

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Branch berhasil diperbarui!', $branch, null, 200);
        } catch (ValidationException $error) {
            // Logging error validasi
            Log::error('Error validasi: ' . $error->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            // Logging error umum
            Log::error('Error saat memperbarui branch: ' . $e->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan saat memperbarui branch.', $request->all(), $e->getMessage(), 500);
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
            $branch = Branch::withTrashed()->find($id);

            // Jika cabang tidak ditemukan
            if (!$branch) {
                Log::info('Cabang tidak ditemukan saat mencoba menghapus', ['id_branch' => $id]);

                return new ResponseApiResource(false, 'Cabang tidak ditemukan!', $id,  $branch, 404);
            }

            // Ubah status is_active_branch menjadi 'inactive'
            $branch->update(['is_active_branch' => 'inactive']);

            // Hapus cabang
            $branch->delete();

            // Log informasi perubahan status Cabang
            Log::info('Cabang berhasil dinonaktifkan', ['id_branch' => $id, 'name_branch' => $branch->name_branch]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Cabang berhasil dinonaktifkan!', $branch, 200);
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
            // Cari cabang berdasarkan ID
            $branch = Branch::withTrashed()->find($id);

            // Jika cabang tidak ditemukan
            if (!$branch) {
                Log::info('Cabang tidak ditemukan saat mencoba dipulihkan', ['id_branch' => $id]);

                return new ResponseApiResource(false, 'Cabang tidak ditemukan!', $id,  $branch, 404);
            }

            // Ubah status is_active_branch menjadi 'active'
            $branch->update(['is_active_branch' => 'active']);

            // Pulihkan cabang
            $branch->restore();

            // Log informasi perubahan status Cabang
            Log::info('Cabang berhasil dipulihkan', ['id_branch' => $id, 'name_branch' => $branch->name_branch]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Cabang berhasil dipulihkan!', $branch, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal memulihkan Cabang', [
                'id_branch' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }

    public function forceDestroy($id)
    {
        try {
            // Cari cabang berdasarkan ID
            $branch = Branch::withTrashed()->find($id);

            // Jika cabang tidak ditemukan
            if (!$branch) {
                Log::info('Cabang tidak ditemukan saat mencoba hapus permanent', ['id_branch' => $id]);

                return new ResponseApiResource(false, 'Cabang tidak ditemukan!', $id,  $branch, 404);
            }

            // Ubah status is_active_branch menjadi 'inactive'
            $branch->update(['is_active_branch' => 'inactive']);

            // Hapus cabang permanent
            $branch->forceDelete();

            // Log informasi perubahan status Cabang
            Log::info('Cabang berhasil hapus permanent', ['id_branch' => $id, 'name_branch' => $branch->name_branch]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Cabang berhasil hapus permanent!', $branch, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal hapus permanent Cabang', [
                'id_branch' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }
}
