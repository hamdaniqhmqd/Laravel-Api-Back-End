<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
// use App\Models\Sanctum\PersonalAccessToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string|min:8',
            ]);

            // Buat user baru
            $user = User::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']), // Hash password
                'fullname_user' => $validated['username'],
                'role_user' => 'kurir',
                'gender_user' => 'male',
                'phone_user' => '081234567891',
                'address_user' => 'Alamat',
                'is_active_user' => 'active',
            ]);

            // Waktu sekarang
            $now = Carbon::now();

            // Buat token dan simpan modelnya
            $token = $user->createToken('apilaundry&rental' . $request->username, ['read', 'write']);
            $plainTextToken = $token->plainTextToken;

            // Ambil model token untuk update last_used_at dan expires_at
            $tokenModel = PersonalAccessToken::findToken($plainTextToken);
            $tokenModel->update([
                'last_used_at' => Carbon::now(),
                'expires_at'   => Carbon::now()->addDay(), // Token hanya berlaku 1 hari
            ]);

            // Response sukses
            return new ResponseApiResource(true, 'User registered successfully', [
                'user'  => $user,
                'token' => $token,
                // 'expires_at' => $now->toDateTimeString(),
            ], null, 200);
        } catch (ValidationException $e) {
            // Jika validasi gagal
            return new ResponseApiResource(false, 'Validation error', [
                'errors' => $e->errors(),
                'status_code' => 422,
            ], 422);
        } catch (\Exception $e) {
            // Jika terjadi error lainnya
            return new ResponseApiResource(false, 'Something went wrong', [
                'error_message' => $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }
}