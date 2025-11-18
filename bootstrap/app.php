<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Confiar en los proxies de Render (X-Forwarded-* headers)
        $middleware->trustProxies(at: '*');
        
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'permiso' => \App\Http\Middleware\ValidarPermisoRol::class,
            'bitacora' => \App\Http\Middleware\BitacoraMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejar errores de constraint UNIQUE en la base de datos
        $exceptions->render(function (QueryException $e, Request $request) {
            // Detectar si es un error de constraint UNIQUE
            if (strpos($e->getMessage(), 'unique constraint') !== false || 
                strpos($e->getMessage(), 'Unique') !== false ||
                strpos($e->getMessage(), 'duplicate') !== false) {
                
                // Para peticiones JSON (API)
                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'Este registro ya existe. No se puede crear un registro duplicado.',
                        'tipo_error' => 'duplicate_entry',
                        'detalles' => 'Uno o más campos tienen valores que ya existen en el sistema.'
                    ], 400);
                }
                
                // Para peticiones web (redirect)
                return redirect()->back()
                    ->with('error', 'Este registro ya existe. No se puede crear un registro duplicado.')
                    ->withInput();
            }
        });
    })->create();