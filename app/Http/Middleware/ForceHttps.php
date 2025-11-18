<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Fuerza HTTPS en producción
     * En Render y otros servicios de hosting, Laravel está detrás de un proxy
     * que maneja SSL, por lo que necesitamos confiar en los headers del proxy
     */
    public function handle(Request $request, Closure $next): Response
    {
        // En producción, forzar HTTPS
        if (app()->environment('production')) {
            // Confiar en el header X-Forwarded-Proto del proxy
            if ($request->header('X-Forwarded-Proto') === 'http') {
                return redirect()->secure($request->getRequestUri(), 301);
            }
        }

        return $next($request);
    }
}
