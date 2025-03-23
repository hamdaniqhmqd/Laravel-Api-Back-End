<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Resources\ResponseApiResource;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        // TEST LOGIN PERTAMA

        // $credentials = $request->validate([
        //     'username' => 'required|string',
        //     'password' => 'required|string|min:8',
        // ]);

        // if (!Auth::attempt($credentials)) {
        //     return new ResponseApiResource(false, 'Invalid is data User', [
        //         'status_code' => 401
        //     ]);
        // }

        // $user = auth()->user();
        // $token = $user->createToken('apilaundry&rental' . $request->username, ['read', 'write']);
        // // $plainTextToken = $token->plainTextToken;

        // return new ResponseApiResource(true, 'Login successful', [
        //     'status_code' => 200,
        //     'token' => $token->plainTextToken
        // ]);

        // TEST LOGIN KEDUAN

        try {
            // Validasi input
            $credentials = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string|min:8',
            ]);

            // Cari user berdasarkan username
            $user = User::where('username', $credentials['username'])->first();

            // Periksa apakah user ada dan memiliki status "active"
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return new ResponseApiResource(false, 'Invalid is data User', [
                    'status_code' => 401
                ], 401);
            }

            // Periksa apakah user memiliki status "active"
            if ($user->is_active_user !== 'active') {
                return new ResponseApiResource(false, 'Account is inactive', [
                    'status_code' => 403
                ], 403);
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

            // Response sukses dengan token baru
            return new ResponseApiResource(true, 'Login successful', [
                'user'  => $user,
                'token' => $plainTextToken
            ]);
        } catch (ValidationException $e) {
            // Jika validasi gagal
            return new ResponseApiResource(false, 'Validation error', [
                'errors' => $e->errors(),
                'status_code' => 422,
            ], 422);
        } catch (\Exception $e) {
            // Tangani error umum
            return new ResponseApiResource(false, 'Something went wrong', [
                'error_message' => $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }
}