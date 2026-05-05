<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsEspecialista
{
    /**
     * Solo permite el acceso a usuarios con rol 'especialista' (terapeutas/médicos).
     * Garantiza el Secreto Médico: solo ellos pueden ver expedientes clínicos.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || ! auth()->user()->esEspecialista()) {
            abort(403, 'Acceso restringido. Esta sección es exclusiva para especialistas.');
        }

        return $next($request);
    }
}
