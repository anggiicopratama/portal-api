<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class PortalTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return $this->unauthorized('Token Bearer tidak ditemukan.');
        }

        try {
            $payload = json_decode(Crypt::decryptString($token), true, flags: JSON_THROW_ON_ERROR);

            $validUsername = isset($payload['username'])
                && hash_equals((string) config('services.portal_api.username'), (string) $payload['username']);
            $notExpired = isset($payload['expires_at']) && (int) $payload['expires_at'] > now()->timestamp;

            if (! $validUsername || ! $notExpired) {
                return $this->unauthorized('Token tidak valid atau sudah kedaluwarsa.');
            }
        } catch (DecryptException|\JsonException) {
            return $this->unauthorized('Token tidak valid atau sudah kedaluwarsa.');
        }

        return $next($request);
    }

    private function unauthorized(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], Response::HTTP_UNAUTHORIZED, [
            'WWW-Authenticate' => 'Bearer',
        ]);
    }
}
