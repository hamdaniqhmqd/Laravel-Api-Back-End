<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Exception;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Resources\AuthApiResource;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            // Tangkap IP dan User Agent
            $ipAddress = $request->ip();
            $device = $request->userAgent();

            // Validasi input
            $credentials = $request->validate([
                'username' => 'required|string|',
                'password' => 'required|string|min:8',
            ]);

            // Cari user berdasarkan username
            $user = User::where('username', $credentials['username'])->first();

            // Periksa apakah user ada dan memiliki status "active"
            if (!$user || !Hash::check($credentials['password'], $user->password) || $user->is_active_user !== 'active') {
                Log::warning(
                    !$user || !Hash::check($credentials['password'], $user->password)
                        ? 'Data pengguna tidak valid : dengan id_user ' . ($user ? $user->id_user : 'unknown')
                        : 'Akun tidak aktif : dengan id_user ' . $user->id_user .
                        ' | IP: ' . $ipAddress . ' | Device: ' . $device
                );

                return new AuthApiResource(
                    false,
                    !$user || !Hash::check($credentials['password'], $user->password)
                        ? 'Data pengguna tidak valid'
                        : 'Akun tidak aktif',
                    null,
                    null,
                    !$user || !Hash::check($credentials['password'], $user->password) ? 401 : 403
                );
            }

            // Hapus semua token sebelumnya
            // $user->tokens()->delete();

            // Buat token baru
            $token = $user->createToken($ipAddress . '_' . $user->username, ['read', 'write']);
            $plainTextToken = $token->plainTextToken;

            Log::info('Login berhasil : dengan id_user ' . $user->id_user .
                ' | IP: ' . $ipAddress .
                ' | Device: ' . $device);

            return new AuthApiResource(true, 'Berhasil masuk', $user, $plainTextToken, null);
        } catch (ValidationException $error) {
            Log::error('Kesalahan validasi : ' . $error->getMessage());

            return new AuthApiResource(false, 'Kesalahan validasi', null, $error->errors(), null, 422);
        } catch (Exception $error) {
            Log::error('Ada yang tidak beres : ' . $error->getMessage());

            return new AuthApiResource(false, 'Ada yang tidak beres', null, null, $error->getMessage(), 500);
        }
    }
}
