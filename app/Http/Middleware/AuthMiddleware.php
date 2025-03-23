<?php

namespace App\Http\Middleware;

use App\Http\Resources\ResponseApiResource;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        try {
            // Periksa apakah user sudah login
            if (!Auth::guard('sanctum')->check()) {
                Log::warning('Unauthorized access attempt detected', [
                    'ip' => $request->ip()
                ]);

                return response()->json(new ResponseApiResource(false, 'Unauthorized', [
                    'status_code' => 401
                ]), 401);
            }

            $user = Auth::guard('sanctum')->user();
            Log::info('User authenticated', ['user_id' => $user->id_user, 'role' => $user->role_user]);

            // Periksa apakah user ada
            if (!$user->role_user) {
                Log::error('User has no assigned role', ['user_id' => $user->id_user]);

                return response()->json(new ResponseApiResource(false, 'User not found', [
                    'status_code' => 404
                ]), 404);
            }

            // Periksa apakah role sesuai
            if ($user->role_user !== $role) {
                Log::warning('User role mismatch', [
                    'user_id' => $user->id_user,
                    'expected_role' => $role,
                    'actual_role' => $user->role_user
                ]);

                return response()->json(new ResponseApiResource(false, 'Forbidden', [
                    'status_code' => 403
                ]), 403);
            }

            Log::info('User authorized successfully', ['user_id' => $user->id_user, 'role' => $user->role_user]);

            return $next($request);
        } catch (Exception $error) { // Pastikan menangkap Exception
            Log::error('Error in AuthMiddleware: ' . $error->getMessage(), [
                'route' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip()
            ]);

            return response()->json(new ResponseApiResource(false, 'Internal Server Error', [
                'status_code' => 500
            ]), 500);
        }
    }
}