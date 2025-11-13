<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use App\Models\administracion\Bitacora;
use App\Models\administracion\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BitacoraController extends Controller
{
    /**
     * Mostrar listado de bitácora
     */
    public function index(Request $request)
    {
        try {
            $query = Bitacora::with('usuario');
            
            // Ordenar por created_at si existe, sino por id_bitacora
            if (Schema::hasColumn('bitacora', 'created_at')) {
                $query->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('id_bitacora', 'desc');
            }

            // Filtro por usuario
            if ($request->has('usuario') && $request->usuario !== '') {
                $query->where('id_usuario', $request->usuario);
            }

            // Filtro por acción
            if ($request->has('accion') && $request->accion !== '') {
                $query->where('accion', 'like', '%' . $request->accion . '%');
            }

            // Filtro por fecha
            if ($request->has('fecha') && $request->fecha !== '') {
                if (Schema::hasColumn('bitacora', 'created_at')) {
                    $query->whereDate('created_at', $request->fecha);
                }
            }

            // Filtro por tabla afectada
            if ($request->has('tabla') && $request->tabla !== '') {
                $query->where('tabla_afectada', $request->tabla);
            }

            // Búsqueda general
            if ($request->has('q') && $request->q !== '') {
                $q = $request->q;
                $query->where(function ($subquery) use ($q) {
                    $subquery->where('accion', 'like', "%$q%")
                             ->orWhere('descripcion', 'like', "%$q%")
                             ->orWhere('ip_origen', 'like', "%$q%");
                });
            }

            $bitacoras = $query->paginate(50);
            
            // Obtener solo id_usuario y username (que existen en la tabla usuario)
            $usuarios = Usuario::select('id_usuario', 'username')->get();

            return view('administracion.bitacora', [
                'bitacoras' => $bitacoras,
                'usuarios' => $usuarios,
                'filtros' => [
                    'usuario' => $request->usuario ?? '',
                    'accion' => $request->accion ?? '',
                    'fecha' => $request->fecha ?? '',
                    'tabla' => $request->tabla ?? '',
                    'q' => $request->q ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar bitácora: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalles de una acción en la bitácora
     */
    public function show($id)
    {
        $bitacora = Bitacora::with('usuario')->findOrFail($id);

        // Decodificar descripción si es JSON
        $descripcion = $bitacora->descripcion;
        try {
            $descripcion = json_decode($descripcion, true) ?? $descripcion;
        } catch (\Exception $e) {
            // Si no es JSON, se deja como está
        }

        return view('administracion.bitacora-detalles', [
            'bitacora' => $bitacora,
            'descripcion' => $descripcion
        ]);
    }

    /**
     * Exportar bitácora a CSV
     */
    public function exportar(Request $request)
    {
        $query = Bitacora::with('usuario');

        // Aplicar filtros
        if ($request->has('usuario') && $request->usuario !== '') {
            $query->where('id_usuario', $request->usuario);
        }
        if ($request->has('fecha') && $request->fecha !== '') {
            $query->whereDate('created_at', $request->fecha);
        }

        $bitacoras = $query->orderBy('created_at', 'desc')->get();

        $csv = "ID,Usuario,Acción,Descripción,IP Origen,Navegador,Tabla Afectada,Registro ID,Fecha\n";

        foreach ($bitacoras as $bitacora) {
            $usuario = $bitacora->usuario ? $bitacora->usuario->nombre . ' ' . $bitacora->usuario->apellido : 'N/A';
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $bitacora->id_bitacora,
                $usuario,
                $bitacora->accion,
                str_replace('"', '""', substr($bitacora->descripcion, 0, 100)),
                $bitacora->ip_origen,
                str_replace('"', '""', substr($bitacora->navegador ?? '', 0, 50)),
                $bitacora->tabla_afectada,
                $bitacora->registro_id,
                $bitacora->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=bitacora_' . date('Y-m-d_His') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ]);
    }

    /**
     * Eliminar registros antiguos de bitácora (limpieza)
     */
    public function limpiar(Request $request)
    {
        $dias = $request->input('dias', 90);
        
        $fecha = now()->subDays($dias);
        $eliminados = Bitacora::where('created_at', '<', $fecha)->delete();

        return redirect()->route('administracion.bitacora.index')
                       ->with('success', "Se eliminaron $eliminados registros de más de $dias días.");
    }

    /**
     * Estadísticas de la bitácora
     */
    public function estadisticas()
    {
        try {
            $hoy = now()->format('Y-m-d');
            $hace_7_dias = now()->subDays(7)->format('Y-m-d');
            $hace_30_dias = now()->subDays(30)->format('Y-m-d');

            $stats = [
                'total_registros' => Bitacora::count(),
                'hoy' => 0,
                'ultimos_7_dias' => 0,
                'ultimos_30_dias' => 0,
                'usuarios_activos' => Bitacora::distinct('id_usuario')->count('id_usuario'),
                'acciones_top_5' => collect(),
                'tablas_top_5' => collect(),
            ];

            // Calcular estadísticas por fecha si la columna existe
            if (Schema::hasColumn('bitacora', 'created_at')) {
                $stats['hoy'] = Bitacora::whereDate('created_at', $hoy)->count();
                $stats['ultimos_7_dias'] = Bitacora::where('created_at', '>=', $hace_7_dias)->count();
                $stats['ultimos_30_dias'] = Bitacora::where('created_at', '>=', $hace_30_dias)->count();
            }

            // Acciones más frecuentes
            $stats['acciones_top_5'] = Bitacora::selectRaw('accion, COUNT(*) as cantidad')
                                                ->groupBy('accion')
                                                ->orderByDesc('cantidad')
                                                ->limit(5)
                                                ->get()
                                                ->pluck('cantidad', 'accion');

            // Tablas más afectadas
            $stats['tablas_top_5'] = Bitacora::selectRaw('tabla_afectada, COUNT(*) as cantidad')
                                              ->whereNotNull('tabla_afectada')
                                              ->groupBy('tabla_afectada')
                                              ->orderByDesc('cantidad')
                                              ->limit(5)
                                              ->get()
                                              ->pluck('cantidad', 'tabla_afectada');

            return view('administracion.bitacora-estadisticas', ['stats' => $stats]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar estadísticas: ' . $e->getMessage());
        }
    }
}
