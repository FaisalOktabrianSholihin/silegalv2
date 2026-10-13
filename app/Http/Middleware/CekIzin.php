<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CekIzin
{
    public function handle(Request $request, Closure $next, string $izin)
    {
        abort_unless($request->user()?->bisa($izin), 403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        return $next($request);
    }
}
