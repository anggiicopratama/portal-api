<?php

namespace App\Service;

use Illuminate\Http\JsonResponse;

class StrResponseService
{
    public static function success(int $code, string $message, $data): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public static function error(int $code, string $message): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
        ]);
    }

    public static function errorArray($code, string $message): array
    {
        return [
            'code' => $code,
            'message' => $message,
        ];
    }
}
