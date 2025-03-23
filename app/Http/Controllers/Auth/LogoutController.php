<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            // Ambil token yang sedang digunakan oleh user
            $user = $request->user();
            $token = $user->currentAccessToken();

            // Periksa apakah token ada
            if (!$token) {
                return new ResponseApiResource(false, 'No active token found', [
                    'status_code' => 400
                ], 400);
            }

            // Update last_used_at untuk menonaktifkan token tanpa menghapusnya
            $token->forceFill(['last_used_at' => now()])->save();

            return new ResponseApiResource(true, 'Logout successful', [
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            return new ResponseApiResource(false, 'Something went wrong', [
                'error_message' => $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }
}
