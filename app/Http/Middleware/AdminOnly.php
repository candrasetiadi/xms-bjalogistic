<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('auth_user.role') !== 'admin') {
            abort(403, 'Hanya admin yang bisa mengakses halaman ini.');
        }

        return $next($request);
    }
}
