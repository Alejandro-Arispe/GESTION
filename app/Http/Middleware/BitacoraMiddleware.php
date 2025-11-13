<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\administracion\Bitacora;
use Illuminate\Support\Facades\Auth;

class BitacoraMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Solo registrar si el usuario está autenticado
        if (Auth::check()) {
            // No registrar GET normales (solo POST, PUT, DELETE)
            if (in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                try {
                    // Extraer información útil del request
                    $ruta = $request->path();
                    $metodo = $request->method();
                    $datos = $request->except(['password', '_token', 'password_confirmation']);
                    
                    // Extraer tabla y ID si es posible
                    $rutaParts = explode('/', $ruta);
                    $tablaAfectada = $rutaParts[0] ?? null;
                    $registroId = $rutaParts[1] ?? null;

                    Bitacora::create([
                        'id_usuario' => Auth::id(),
                        'accion' => "{$metodo} {$ruta}",
                        'descripcion' => json_encode([
                            'metodo' => $metodo,
                            'ruta' => $ruta,
                            'datos' => $datos,
                            'status' => $response->status()
                        ]),
                        'ip_origen' => $request->ip(),
                        'navegador' => $request->userAgent(),
                        'tabla_afectada' => $tablaAfectada,
                        'registro_id' => is_numeric($registroId) ? $registroId : null
                    ]);
                } catch (\Exception $e) {
                    // Silenciosamente ignorar errores de bitácora
                    \Log::error('Error registrando bitácora: ' . $e->getMessage());
                }
            }
        }

        return $response;
    }
}
