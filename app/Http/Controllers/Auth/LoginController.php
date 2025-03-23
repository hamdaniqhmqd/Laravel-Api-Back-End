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
use App\Http\Resources\ResponseApiResource;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            // Validasi input
            $credentials = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string|min:8',
            ]);

            // Cari user berdasarkan username
            $user = User::where('username', $credentials['username'])->first();

            // Periksa apakah user ada dan memiliki status "active"
            if (!$user || !Hash::check($credentials['password'], $user->password) || $user->is_active_user !== 'active') {

                Log::warning(
                    !$user || !Hash::check($credentials['password'], $user->password)
                        ? 'Data pengguna tidak valid : dengan id_user ' . $user->id_user
                        : 'Akun tidak aktif : dengan id_user ' . $user->id_user
                );

                return new ResponseApiResource(
                    false,
                    !$user || !Hash::check($credentials['password'], $user->password)
                        ? 'Data pengguna tidak valid'
                        : 'Akun tidak aktif',
                    null,
                    !$user || !Hash::check($credentials['password'], $user->password) ? 401 : 403
                );
            }

            // Periksa token sebelumnya
            $existingToken = $user->tokens()->latest()->first();

            if ($existingToken) {
                // Update last_used_at pada token sebelumnya
                $existingToken->forceFill(['last_used_at' => now()])->save();
            }

            // Buat token baru
            $token = $user->createToken('apilaundry&rental' . $user->username, ['read', 'write']);
            $plainTextToken = $token->plainTextToken;

            // Informasi data token
            $tokenModel = PersonalAccessToken::findToken($plainTextToken);

            Log::info('Login berhasil : dengan id_user ' . $user->id_user);

            // Response sukses dengan token baru
            return new ResponseApiResource(true, 'Berhasil masuk', [
                'user'  => $user,
                'token' => $plainTextToken,
            ], null);
        } catch (ValidationException $error) {
            // Jika validasi gagal
            Log::error('Kesalahan validasi : ' . $error->getMessage());

            return new ResponseApiResource(false, 'Kesalahan validasi', null, $error->errors(), 422);
        } catch (Exception $error) {
            // Tangani error umum
            Log::error('Ada yang tidak beres : ' . $error->getMessage());

            return new ResponseApiResource(false, 'Ada yang tidak beres', null, $error->getMessage(), 500);
        }
    }
}