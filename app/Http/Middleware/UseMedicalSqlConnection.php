<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UseMedicalSqlConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        $previousConnection = DB::getDefaultConnection();
        DB::setDefaultConnection('medical_sql');

        try {
            return $next($request);
        } finally {
            DB::setDefaultConnection($previousConnection);
        }
    }
}
