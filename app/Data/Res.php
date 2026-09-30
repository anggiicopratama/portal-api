<?php

namespace App\Data;

use Symfony\Component\HttpFoundation\Response;

class Res
{
    public static function json(string $message, $data = null, $eloquent = true): object
    {
        $output = [];
        $output['message'] = $message;
        $output['data'] = $data;

        return response()->json($output, Response::HTTP_OK);
    }

    public static function errorJSON($message, $status)
    {
        $output = compact('message', 'status');

        return response()->json($output, $status);
    }
}
