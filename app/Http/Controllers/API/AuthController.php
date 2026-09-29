<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = (string) config('services.portal_api.username');
        $password = (string) config('services.portal_api.password');

        if ($username === '' || $password === '') {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial API belum dikonfigurasi.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (! hash_equals($username, $credentials['username'])
            || ! hash_equals($password, $credentials['password'])) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $expiresAt = now()->addMinutes(config('services.portal_api.token_ttl'));
        $token = Crypt::encryptString(json_encode([
            'username' => $username,
            'expires_at' => $expiresAt->timestamp,
        ], JSON_THROW_ON_ERROR));

        return response()->json([
            'success' => true,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt->toIso8601String(),
        ]);
    }
}
