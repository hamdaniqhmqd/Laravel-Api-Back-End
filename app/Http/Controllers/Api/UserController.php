<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use App\Models\Logging;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * index
     *
     * @return void
     */

    public function index()
    {
        try {
            // Dapatkan semua pengguna kecuali yang memiliki is_active_user = 'inactive'
            $users = User::where('is_active_user', '!=', 'inactive')->get();

            Log::info('Sukses menampilkan data user yang aktif');

            // Return collection of users as a resource
            return new ResponseApiResource(true, 'Daftar Data Pengguna Aktif', $users, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data Pengguna Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data Pengguna Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data User Tidak Ditemukan!', null, $error->getMessage(), 404);
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
            //get all users
            $users = User::get();

            Log::info('Sukses menampilkan data user');

            //return collection of users as a resource
            return new ResponseApiResource(true, 'Daftar seluruh Data Pengguna', $users, null, 200);
        } catch (Exception $error) {
            Log::error("Daftar Data Pengguna Gagal " . $error->getMessage());

            Logging::record(
                Auth::guard('sanctum')->user(),
                "Daftar Data Pengguna Gagal " . $error->getMessage()
            );

            return new ResponseApiResource(false, 'Data User Tidak Ditemukan!', null, $error->getMessage(), 404);
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
                'id_branch_user' => 'nullable|exists:branches,id_branch',
                'username' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:8',
                'fullname_user' => 'required|string|max:255',
                'role_user' => 'required|in:admin,owner,kurir',
                'gender_user' => 'required|in:male,female',
                'phone_user' => 'nullable|string|max:15',
                'address_user' => 'nullable|string',
                'is_active_user' => 'required|in:active,inactive',
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::warning('Validasi gagal : ' . $validator->errors()->toJson());

                return new ResponseApiResource(false, 'Validasi gagal', $request->all(), $validator->errors());
            }

            // Jika role adalah 'owner', pastikan id_branch_user bernilai null
            // if ($request->role_user === 'owner' && $request->id_branch_user !== null) {
            //     $request->merge(['id_branch_user' => null]);
            // }

            // Membuat user baru
            $user = User::create([
                'id_branch_user' => $request->id_branch_user,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'fullname_user' => $request->fullname_user,
                'role_user' => $request->role_user,
                'gender_user' => $request->gender_user,
                'phone_user' => $request->phone_user,
                'address_user' => $request->address_user,
                'is_active_user' => $request->is_active_user,
            ]);

            // Kembalikan response sukses
            Log::info('User berhasil ditambahkan! dengan id_user ' . $user->id_user);

            return new ResponseApiResource(true, 'User berhasil ditambahkan!', $user, null, 200);
        } catch (ValidationException $error) {
            // Logging error untuk debugging
            Log::error('Error saat menambahkan user : ' . $error->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan user.', $request->all(), $error->getMessage(), 500);
        } catch (Exception $e) {
            // Logging error untuk debugging
            Log::error('Error saat menambahkan user : ' . $e->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan saat menambahkan user.', $request->all(), $e->getMessage(), 500);
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
            // Cari user berdasarkan ID
            $user = User::find($id);

            // Periksa apakah user ditemukan
            if (!$user) {
                Log::info('User tidak ditemukan dengan id ' . $id);

                return new ResponseApiResource(false, 'User tidak ditemukan', null, 404);
            }

            // Log info jika user ditemukan
            Log::info('Detail user ditemukan', ['id' => $user->id_user, 'nama' => $user->name]);

            // Return data user sebagai resource
            return new ResponseApiResource(true, 'Detail Data User!', $user, null, 200);
        } catch (Exception $e) {
            // Log error jika terjadi masalah
            Log::error('Gagal mengambil data user', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Cari user berdasarkan ID
            $user = User::find($id);

            // Jika user tidak ditemukan
            if (!$user) {
                Log::info('User tidak ditemukan', ['id_user' => $id]);
                return new ResponseApiResource(false, 'User tidak ditemukan!', null, 404);
            }

            // Validasi input sesuai schema
            $validator = Validator::make($request->all(), [
                'username'       => 'required|string|max:255|unique:users,username,' . $id . ',id_user',
                'password'       => 'nullable|string|min:8',
                'fullname_user'  => 'required|string|max:255',
                'role_user'      => 'required|in:admin,owner,kurir',
                'gender_user'    => 'required|in:male,female',
                'phone_user'     => 'nullable|string|max:20',
                'address_user'   => 'nullable|string',
                'is_active_user' => 'required|in:active,inactive',
            ]);

            // Jika validasi gagal, kembalikan response error
            if ($validator->fails()) {
                Log::info('Validasi gagal saat update user', ['id_user' => $id, 'errors' => $validator->errors()]);

                return new ResponseApiResource(false, 'Validasi gagal', $validator->errors(), 422);
            }

            // Perbarui data user
            $user->update([
                'username'       => $request->username,
                'password'       => $request->password ? Hash::make($request->password) : $user->password,
                'fullname_user'  => $request->fullname_user,
                'role_user'      => $request->role_user,
                'gender_user'    => $request->gender_user,
                'phone_user'     => $request->phone_user,
                'address_user'   => $request->address_user,
                'is_active_user' => $request->is_active_user,
            ]);

            // Log info jika update berhasil
            Log::info('User berhasil diperbarui', ['id_user' => $id, 'username' => $user->username]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'User berhasil diperbarui!', $user, $validator->errors(), 200);
        } catch (ValidationException $error) {
            // Logging error untuk debugging
            Log::error('Error saat memperbarui user : ' . $error->getMessage());

            return new ResponseApiResource(false, 'Terjadi kesalahan saat memperbarui user.', $id, $error->getMessage(), 500);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal memperbarui user', [
                'id_user' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
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
            // Cari user berdasarkan ID
            $user = User::find($id);

            // Jika user tidak ditemukan
            if (!$user) {
                Log::info('User tidak ditemukan saat mencoba menghapus', ['id_user' => $id]);

                return new ResponseApiResource(false, 'User tidak ditemukan!', $id,  $user, 404);
            }

            // Ubah status is_active_user menjadi 'inactive'
            $user->update(['is_active_user' => 'inactive']);

            // Log informasi perubahan status user
            Log::info('User berhasil dinonaktifkan', ['id_user' => $id, 'username' => $user->username]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'User berhasil dinonaktifkan!', $user, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal menonaktifkan user', [
                'id_user' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', $id, $e->getMessage(), 500);
        }
    }

    public function resetPassword($id)
    {
        try {
            // Cari user berdasarkan ID
            $user = User::find($id);

            // Jika user tidak ditemukan
            if (!$user) {
                Log::info('User tidak ditemukan saat mencoba mereset password', ['id_user' => $id]);

                return new ResponseApiResource(false, 'User tidak ditemukan!', null, null, 404);
            }

            // Reset password user ke default
            $newPassword = 'password123';
            $user->update(['password' => Hash::make($newPassword)]);

            // Log informasi perubahan password
            Log::info('Password user berhasil direset', ['id_user' => $id, 'username' => $user->username]);

            // Kembalikan response sukses
            return new ResponseApiResource(true, 'Password berhasil direset!', $user, null, 200);
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Gagal mereset password user', [
                'id_user' => $id,
                'error'   => $e->getMessage()
            ]);

            return new ResponseApiResource(false, 'Terjadi kesalahan pada server', null, $e->getMessage(), 500);
        }
    }
}
