<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseApiResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class LogoutController extends Controller
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

            // Ambil token yang sedang digunakan oleh user
            $user = $request->user();
            $token = $user->currentAccessToken();

            // Periksa apakah token ada dan kolom last_used_at belum terisi
            if (!$token) {
                Log::warning('Token tidak ditemukan : dengan id_user ' . $user->id_user .
                    ' | IP: ' . $ipAddress . ' | Device: ' . $device);

                return new ResponseApiResource(
                    false,
                    'Token tidak ditemukan',
                    $user,
                    'Token tidak ditemukan',
                    404
                );
            }

            // Hapus token
            // $token->delete();

            // Update kolom expired pada token, misalnya set ke waktu sekarang
            $token->forceFill([
                'expires_at' => Carbon::now(), // atau gunakan true jika expired berupa boolean
            ])->save();

            Log::info('Logout berhasil : dengan id_user ' . $user->id_user);

            return new ResponseApiResource(true, 'Logout berhasil', $user, null, 200);
        } catch (Exception $e) {
            Log::error('Logout gagal : dengan id_user ' . $user->id_user . ' : ' . $e->getMessage());

            return new ResponseApiResource(false, 'Ada yang tidak beres', $user, $e->getMessage(), 500);
        }
    }
}
