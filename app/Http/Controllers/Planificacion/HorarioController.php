<?php

namespace App\Http\Controllers\Planificacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Planificacion\Horario;
use App\Models\ConfiguracionAcademica\Grupo;
use App\Models\ConfiguracionAcademica\Aula;
use App\Services\ClassroomAssignmentEngine;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class HorarioController extends Controller
{
    /**
     * Listar horarios con filtros
     */
    public function index(Request $request)
    {
        try {
            $query = Horario::with(['grupo.materia', 'grupo.docente', 'aula']);

            // Filtro por día
            if ($request->has('dia_semana')) {
                $query->where('dia_semana', $request->dia_semana);
            }

            // Filtro por aula
            if ($request->has('id_aula')) {
                $query->where('id_aula', $request->id_aula);
            }

            // Filtro por docente (a través del grupo)
            if ($request->has('id_docente')) {
                $query->whereHas('grupo', function($q) use ($request) {
                    $q->where('id_docente', $request->id_docente);
                });
            }

            // Filtro por grupo
            if ($request->has('id_grupo')) {
                $query->where('id_grupo', $request->id_grupo);
            }

            $horarios = $query->orderBy('dia_semana')
                             ->orderBy('hora_inicio')
                             ->get();

            return response()->json([
                'horarios' => $horarios
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener horarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear horario con validación de conflictos
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_grupo' => 'required|exists:grupo,id_grupo',
                'id_aula' => 'required|exists:aula,id_aula',
                'dia_semana' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'tipo_asignacion' => 'nullable|in:Manual,Automática'
            ]);

            // Validar conflictos ANTES de guardar
            $conflictos = $this->validarConflictosInterno($request->all());

            if (!empty($conflictos)) {
                return response()->json([
                    'message' => 'Existen conflictos de horario. No se puede registrar.',
                    'conflictos' => $conflictos,
                    'puede_guardar' => false
                ], 400);
            }

            // Si no hay conflictos, crear el horario
            $horario = Horario::create($request->all());

            return response()->json([
                'message' => 'Horario creado exitosamente',
                'horario' => $horario->load(['grupo.materia', 'grupo.docente', 'aula']),
                'puede_guardar' => true
            ], 201);
        } catch (QueryException $e) {
            if (strpos($e->getMessage(), 'unique constraint') !== false || 
                strpos($e->getMessage(), 'Unique') !== false ||
                strpos($e->getMessage(), 'duplicate') !== false) {
                return response()->json([
                    'message' => 'Este horario ya existe en la base de datos.',
                    'conflictos' => [[
                        'tipo' => 'duplicado',
                        'mensaje' => 'Ya existe este horario exacto en el sistema'
                    ]],
                    'puede_guardar' => false
                ], 400);
            }
            
            return response()->json([
                'message' => 'Error en la base de datos al crear horario',
                'error' => $e->getMessage(),
                'puede_guardar' => false
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al crear horario',
                'error' => $e->getMessage(),
                'puede_guardar' => false
            ], 500);
        }
    }

    /**
     * Mostrar horario específico
     */
    public function show($id)
    {
        try {
            $horario = Horario::with(['grupo.materia', 'grupo.docente', 'aula'])->findOrFail($id);

            return response()->json([
                'horario' => $horario
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Horario no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Actualizar horario con validación
     */
    public function update(Request $request, $id)
    {
        try {
            $horario = Horario::findOrFail($id);

            $request->validate([
                'id_grupo' => 'required|exists:grupo,id_grupo',
                'id_aula' => 'required|exists:aula,id_aula',
                'dia_semana' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'tipo_asignacion' => 'nullable|in:Manual,Automática'
            ]);

            // Validar conflictos excluyendo el horario actual
            $conflictos = $this->validarConflictosInterno($request->all(), $id);

            if (!empty($conflictos)) {
                return response()->json([
                    'message' => 'Existen conflictos de horario. No se puede actualizar.',
                    'conflictos' => $conflictos,
                    'puede_guardar' => false
                ], 400);
            }

            $horario->update($request->all());

            return response()->json([
                'message' => 'Horario actualizado exitosamente',
                'horario' => $horario->load(['grupo.materia', 'grupo.docente', 'aula']),
                'puede_guardar' => true
            ], 200);
        } catch (QueryException $e) {
            if (strpos($e->getMessage(), 'unique constraint') !== false || 
                strpos($e->getMessage(), 'Unique') !== false ||
                strpos($e->getMessage(), 'duplicate') !== false) {
                return response()->json([
                    'message' => 'Este horario ya existe en la base de datos.',
                    'conflictos' => [[
                        'tipo' => 'duplicado',
                        'mensaje' => 'Ya existe este horario exacto en el sistema'
                    ]],
                    'puede_guardar' => false
                ], 400);
            }
            
            return response()->json([
                'message' => 'Error en la base de datos al actualizar horario',
                'error' => $e->getMessage(),
                'puede_guardar' => false
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar horario',
                'error' => $e->getMessage(),
                'puede_guardar' => false
            ], 500);
        }
    }

    /**
     * Eliminar horario
     */
    public function destroy($id)
    {
        try {
            $horario = Horario::findOrFail($id);
            $horario->delete();

            return response()->json([
                'message' => 'Horario eliminado exitosamente'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar horario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validar conflictos de horario (endpoint público) - MEJORADO
     */
    public function validarConflictos(Request $request)
    {
        try {
            $request->validate([
                'id_grupo' => 'required|exists:grupo,id_grupo',
                'id_aula' => 'required|exists:aula,id_aula',
                'dia_semana' => 'required|string',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'id_horario_excluir' => 'nullable|exists:horario,id_horario'
            ]);

            $conflictos = $this->validarConflictosInterno(
                $request->all(), 
                $request->id_horario_excluir ?? null
            );

            return response()->json([
                'tiene_conflictos' => !empty($conflictos),
                'cantidad_conflictos' => count($conflictos),
                'conflictos' => $conflictos,
                'puede_guardar' => empty($conflictos),
                'debug' => [
                    'dia' => $request->dia_semana,
                    'hora_inicio' => $request->hora_inicio,
                    'hora_fin' => $request->hora_fin,
                    'aula' => $request->id_aula,
                    'grupo' => $request->id_grupo
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al validar conflictos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método interno para validar conflictos
     * Valida: 1. Conflicto de AULA 2. Conflicto de DOCENTE 3. Conflicto de GRUPO
     */
    private function validarConflictosInterno($data, $idExcluir = null)
    {
        $conflictos = [];
        
        try {
            $grupo = Grupo::with('docente', 'materia')->findOrFail($data['id_grupo']);
        } catch (Exception $e) {
            return [[
                'tipo' => 'error',
                'mensaje' => 'Grupo no encontrado'
            ]];
        }

        $horaInicio = $data['hora_inicio'];
        $horaFin = $data['hora_fin'];
        $diaSemana = $data['dia_semana'];
        $idAula = $data['id_aula'];

        // Convertir a minutos desde medianoche para comparación más precisa
        $inicioMinutos = $this->horaAMinutos($horaInicio);
        $finMinutos = $this->horaAMinutos($horaFin);

        // ============================================================
        // 1. VALIDACIÓN DE CONFLICTO DE AULA
        // ============================================================
        $horariosAula = Horario::where('id_aula', $idAula)
            ->where('dia_semana', $diaSemana)
            ->when($idExcluir, function($q) use ($idExcluir) {
                $q->where('id_horario', '!=', $idExcluir);
            })
            ->with(['grupo.docente', 'grupo.materia', 'aula'])
            ->get();

        foreach ($horariosAula as $horario) {
            $horarioInicio = $this->horaAMinutos($horario->hora_inicio);
            $horarioFin = $this->horaAMinutos($horario->hora_fin);

            // Verificar solapamiento: NO($finNueva <= $inicioExistente OR $inicioNueva >= $finExistente)
            if (!($finMinutos <= $horarioInicio || $inicioMinutos >= $horarioFin)) {
                $conflictos[] = [
                    'tipo' => 'aula',
                    'severidad' => 'error',
                    'titulo' => 'Conflicto de Aula',
                    'mensaje' => 'El aula ' . $horario->aula->nro . ' ya está ocupada en este horario',
                    'detalle' => [
                        'aula_ocupada' => $horario->aula->nro,
                        'docente_ocupante' => $horario->grupo->docente->nombre ?? 'Sin asignar',
                        'materia_ocupante' => $horario->grupo->materia->nombre ?? 'Sin materia',
                        'grupo_ocupante' => $horario->grupo->nombre,
                        'hora_conflicto' => $horario->hora_inicio . ' - ' . $horario->hora_fin,
                        'dia' => $horario->dia_semana
                    ]
                ];
                break;
            }
        }

        // ============================================================
        // 2. VALIDACIÓN DE CONFLICTO DE DOCENTE
        // ============================================================
        if ($grupo->id_docente) {
            $horariosDocente = Horario::whereHas('grupo', function($q) use ($grupo) {
                    $q->where('id_docente', $grupo->id_docente);
                })
                ->where('dia_semana', $diaSemana)
                ->when($idExcluir, function($q) use ($idExcluir) {
                    $q->where('id_horario', '!=', $idExcluir);
                })
                ->with(['grupo.docente', 'grupo.materia', 'aula'])
                ->get();

            foreach ($horariosDocente as $horario) {
                $horarioInicio = $this->horaAMinutos($horario->hora_inicio);
                $horarioFin = $this->horaAMinutos($horario->hora_fin);

                if (!($finMinutos <= $horarioInicio || $inicioMinutos >= $horarioFin)) {
                    $conflictos[] = [
                        'tipo' => 'docente',
                        'severidad' => 'error',
                        'titulo' => 'Conflicto de Docente',
                        'mensaje' => 'El docente ' . $grupo->docente->nombre . ' ya tiene clase en este horario',
                        'detalle' => [
                            'docente' => $grupo->docente->nombre,
                            'materia_existente' => $horario->grupo->materia->nombre ?? 'Sin materia',
                            'grupo_existente' => $horario->grupo->nombre,
                            'aula_existente' => $horario->aula->nro,
                            'hora_conflicto' => $horario->hora_inicio . ' - ' . $horario->hora_fin,
                            'dia' => $horario->dia_semana,
                            'nueva_materia' => $grupo->materia->nombre ?? 'Sin materia',
                            'nueva_aula' => $idAula
                        ]
                    ];
                    break;
                }
            }
        }

        // ============================================================
        // 3. VALIDACIÓN DE CONFLICTO DE GRUPO
        // ============================================================
        $horariosGrupo = Horario::where('id_grupo', $data['id_grupo'])
            ->where('dia_semana', $diaSemana)
            ->when($idExcluir, function($q) use ($idExcluir) {
                $q->where('id_horario', '!=', $idExcluir);
            })
            ->with(['grupo.materia', 'grupo.docente', 'aula'])
            ->get();

        foreach ($horariosGrupo as $horario) {
            $horarioInicio = $this->horaAMinutos($horario->hora_inicio);
            $horarioFin = $this->horaAMinutos($horario->hora_fin);

            if (!($finMinutos <= $horarioInicio || $inicioMinutos >= $horarioFin)) {
                $conflictos[] = [
                    'tipo' => 'grupo',
                    'severidad' => 'error',
                    'titulo' => 'Conflicto de Grupo',
                    'mensaje' => 'El grupo ' . $grupo->nombre . ' ya tiene clase en este horario',
                    'detalle' => [
                        'grupo' => $grupo->nombre,
                        'materia_existente' => $horario->grupo->materia->nombre ?? 'Sin materia',
                        'aula_existente' => $horario->aula->nro,
                        'docente_existente' => $horario->grupo->docente->nombre ?? 'Sin asignar',
                        'hora_conflicto' => $horario->hora_inicio . ' - ' . $horario->hora_fin,
                        'dia' => $horario->dia_semana,
                        'nueva_aula' => $idAula
                    ]
                ];
                break;
            }
        }

        return $conflictos;
    }

    /**
     * Convierte hora en formato HH:mm a minutos desde medianoche
     */
    private function horaAMinutos($hora)
    {
        list($horas, $minutos) = explode(':', $hora);
        return intval($horas) * 60 + intval($minutos);
    }

    /**
     * Asignación automática de horarios (NUEVA - con algoritmo inteligente)
     * Distribuye aulas según:
     * 1. Carga horaria del docente
     * 2. Prioridad: Primer piso > Laboratorios para materias que requieran
     * 3. Sin conflictos de docente ni aula
     */
    public function asignarAutomatico(Request $request)
    {
        try {
            $request->validate([
                'id_gestion' => 'required|exists:gestion_academica,id_gestion'
            ]);

            $engine = new ClassroomAssignmentEngine();
            $resultado = $engine->asignarAulasInteligente($request->id_gestion);

            if (isset($resultado['error'])) {
                return response()->json($resultado, 400);
            }

            return response()->json([
                'message' => 'Asignación automática inteligente completada',
                'resumen' => $resultado
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error en la asignación automática',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información de carga horaria por docente
     */
    public function obtenerCargaHoraria(Request $request)
    {
        try {
            $request->validate([
                'id_gestion' => 'required|exists:gestion_academica,id_gestion'
            ]);

            $engine = new ClassroomAssignmentEngine();
            $cargaHoraria = $engine->obtenerCargaHorariaDocentes($request->id_gestion);

            return response()->json([
                'carga_horaria' => $cargaHoraria
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener carga horaria',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DEBUG: Obtener todos los horarios de un aula en un día específico
     */
    public function debugAula($idAula, Request $request)
    {
        try {
            $request->validate([
                'dia_semana' => 'nullable|string'
            ]);

            $query = Horario::where('id_aula', $idAula)
                ->with(['grupo.docente', 'grupo.materia', 'aula']);

            if ($request->dia_semana) {
                $query->where('dia_semana', $request->dia_semana);
            }

            $horarios = $query->orderBy('dia_semana')
                             ->orderBy('hora_inicio')
                             ->get();

            return response()->json([
                'aula_id' => $idAula,
                'cantidad' => $horarios->count(),
                'horarios' => $horarios,
                'filtro_dia' => $request->dia_semana ?? 'Sin filtro'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener horarios del aula',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}