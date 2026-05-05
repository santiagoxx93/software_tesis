<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsAdmin
{
    /**
     * Solo permite el acceso a usuarios con rol 'admin' (recepción).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->esAdmin()) {
            abort(403, 'Acceso restringido. Se requiere rol de administrador.');
        }

        return $next($request);
    }
}
