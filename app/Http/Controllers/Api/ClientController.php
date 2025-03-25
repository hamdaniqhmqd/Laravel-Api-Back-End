<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\Client;
use App\Models\Logging;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    /**
     * index
     *
     * @return void
     */

    public function index()
    {
        try {
            // Dapatkan semua client kecuali yang memiliki is_active_client = 'inactive'
            $clients = Client::where('is_active_client', '!=', 'inactive')->latest()->get();

            Log::info('Sukses menampilkan data client yang aktif');

            // Kembalikan data client sebagai resource
            return new ResponseApiResource(true, 'Daftar Data client Aktif', $clients, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data client Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data client Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data Client Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            // Mengambil semua client
            $clients =  Client::withTrashed()->latest()->get();

            Log::info('Sukses menampilkan seluruh data client');

            // Kembalikan data client sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data client', $clients, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data client Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data client Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data client Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            // Mengambil semua client
            $clients =  Client::onlyTrashed()->latest()->get();

            Log::info('Sukses menampilkan data client yang dihapus');

            // Kembalikan data client sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data client yang dihapus', $clients, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data client Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data client Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data client Tidak Ditemukan!', null, $error->getMessage(), 404);
        }
    }

    /**
     * getBranch
     *
     * @return void
     */
    public function getBrachWithClient()
    {
        try {
            // Mengambil semua client
            $clients =  Client::with('branch')->get();

            Log::info('Sukses menampilkan data client');

            // Kembalikan data client sebagai resource
            return new ResponseApiResource(true, 'Daftar seluruh Data client', $clients, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data client Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data client Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data client Tidak Ditemukan!', null, $error->getMessage(), 404);
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
                'name_client' => 'required|string|max:255',
                'address_client' => 'nullable|string',
                'phone_client' => 'nullable|string|max:15',
                'is_active_client' => 'required|in:active,inactive',
                'id_branch_client' => 'nullable|exists:branches,id_branch'
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Membuat client baru
            $client = Client::create([
                'name_client' => $request->name_client,
                'address_client' => $request->address_client,
                'phone_client' => $request->phone_client,
                'is_active_client' => $request->is_active_client,
                'id_branch_client' => $request->id_branch_client,
            ]);

            // Kembalikan response sukses
            Log::info('Client berhasil ditambahkan dengan id_client ' . $client->id_client);

            return new ResponseApiResource(true, 'Client berhasil ditambahkan!', $client, null, 201);
        } catch (ValidationException $error) {
            // Logging error untuk debugging
            Log::error('Error validasi: ' . $error->getMessage());
            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            // Logging error untuk debugging
            Log::error('Error saat menambahkan client: ' . $e->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal Menambah Data client: " . $e->getMessage()
            );

            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan client.', $request->all(), $e->getMessage(), 500);
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
            $client = Client::find($id);

            // Periksa apakah client ditemukan
            if (!$client) {
                Log::info('Client tidak ditemukan dengan id ' . $id);

                return new ResponseApiResource(false, 'client tidak ditemukan', null, 404);
            }

            // Log info jika client ditemukan
            Log::info('Detail client ditemukan', ['id' => $client->id_client, 'nama' => $client->name_client]);

            // Return data client sebagai resource
            return new ResponseApiResource(true, 'Detail Data client!', $client, null, 200);
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
            // Validasi input
            $validator = Validator::make($request->all(), [
                'name_client' => 'required|string|max:255',
                'address_client' => 'nullable|string',
                'phone_client' => 'nullable|string|max:15',
                'is_active_client' => 'required|in:active,inactive',
                'id_branch_client' => 'nullable|exists:branches,id_branch'
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal: ' . $validator->errors()->toJson());
                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Cari client berdasarkan ID
            $client = Client::find($id);
            if (!$client) {
                return new ResponseApiResource(false, 'Client tidak ditemukan.', [], null, 404);
            }

            // Update data client
            $client->update([
                'name_client' => $request->name_client,
                'address_client' => $request->address_client,
                'phone_client' => $request->phone_client,
                'is_active_client' => $request->is_active_client,
                'id_branch_client' => $request->id_branch_client,
            ]);

            // Logging berhasil
            Log::info('Client dengan id_client ' . $id . ' berhasil diperbarui.');

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Client berhasil diperbarui!', $client, null, 200);
        } catch (ValidationException $error) {
            // Logging error validasi
            Log::error('Error validasi: ' . $error->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan validasi.', $request->all(), $error->getMessage(), 422);
        } catch (Exception $e) {
            // Logging error umum
            Log::error('Error saat memperbarui branch: ' . $e->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal Memperbarui Data branch: " . $e->getMessage()
            );

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
            // Cari Client berdasarkan ID
            $client = Client::find($id);

            // Jika Client tidak ditemukan
            if (!$client) {
                Log::info('Client tidak ditemukan saat mencoba menghapus', ['id_client' => $id]);

                return new ResponseApiResource(false, 'Client tidak ditemukan!', $id,  $client, 404);
            }

            // Ubah status is_active_client menjadi 'inactive'
            $client->update(['is_active_client' => 'inactive']);

            // Hapus
            $client->delete();

            // Log informasi perubahan status Client
            Log::info('Client berhasil dinonaktifkan', ['id_client' => $id, 'name_client' => $client->name_client]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Client berhasil dinonaktifkan!', $client, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal menonaktifkan Client', [
                'id_client' => $id,
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
            $client = Client::withTrashed()->find($id);

            // Jika Client tidak ditemukan
            if (!$client) {
                Log::info('Client tidak ditemukan saat mencoba dipulihkan', ['id_client' => $id]);

                return new ResponseApiResource(false, 'Client tidak ditemukan!', $id,  $client, 404);
            }

            // Ubah status is_active_client menjadi 'active'
            $client->update(['is_active_client' => 'active']);

            // Restore Client
            $client->restore();

            // Log informasi perubahan status Client
            Log::info('Client berhasil dipulihkan', ['id_client' => $id, 'name_client' => $client->name_client]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Client berhasil dipulihkan!', $client, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal memulihkan Client', [
                'id_client' => $id,
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
            $client = Client::withTrashed()->find($id);

            // Jika Client tidak ditemukan
            if (!$client) {
                Log::info('Client tidak ditemukan saat mencoba hapus permanent', ['id_client' => $id]);

                return new ResponseApiResource(false, 'Client tidak ditemukan!', $id,  $client, 404);
            }

            // Ubah status is_active_client menjadi 'inactive'
            $client->update(['is_active_client' => 'inactive']);

            // Hapus permanen Client
            $client->forceDelete();

            // Log informasi perubahan status Client
            Log::info('Client berhasil hapus permanent', ['id_client' => $id, 'name_client' => $client->name_client]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Client berhasil hapus permanent!', $client, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal hapus permanent Client', [
                'id_client' => $id,
                'error'   => $e->getMessage()
            ]);

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Gagal hapus permanent Client: " . $e->getMessage()
            );

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }
}
