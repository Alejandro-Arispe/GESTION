@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-5">
                <i class="fas fa-chart-line"></i> Estadísticas de Bitácora
            </h1>
            <p class="text-muted">Análisis de actividad del sistema</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('administracion.bitacora.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h3 class="display-6 text-primary fw-bold">{{ $stats['total_registros'] }}</h3>
                    <p class="text-muted mb-0">Total de Registros</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h3 class="display-6 text-success fw-bold">{{ $stats['hoy'] }}</h3>
                    <p class="text-muted mb-0">Registros Hoy</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h3 class="display-6 text-info fw-bold">{{ $stats['ultimos_7_dias'] }}</h3>
                    <p class="text-muted mb-0">Últimos 7 Días</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h3 class="display-6 text-warning fw-bold">{{ $stats['usuarios_activos'] }}</h3>
                    <p class="text-muted mb-0">Usuarios Activos</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Acciones Más Frecuentes -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-mouse"></i> Top 5 Acciones</h6>
                </div>
                <div class="card-body">
                    @forelse($stats['acciones_top_5'] as $accion => $cantidad)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <small><strong>{{ $accion }}</strong></small>
                                <small class="text-muted">{{ $cantidad }}</small>
                            </div>
                            <div class="progress" style="height: 20px;">
                                @php
                                    $max = $stats['acciones_top_5']->max();
                                    $percentage = ($cantidad / $max) * 100;
                                @endphp
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">Sin datos</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tablas Más Afectadas -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-database"></i> Top 5 Tablas</h6>
                </div>
                <div class="card-body">
                    @forelse($stats['tablas_top_5'] as $tabla => $cantidad)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <small><strong>{{ $tabla }}</strong></small>
                                <small class="text-muted">{{ $cantidad }}</small>
                            </div>
                            <div class="progress" style="height: 20px;">
                                @php
                                    $max = $stats['tablas_top_5']->max();
                                    $percentage = ($cantidad / $max) * 100;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">Sin datos</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen Temporal -->
    <div class="card shadow-sm">
        <div class="card-header bg-info text-white">
            <h6 class="mb-0"><i class="fas fa-calendar"></i> Resumen Temporal</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-3">Registros por Período</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Últimos 30 días:</strong>
                            <span class="badge bg-secondary">{{ $stats['ultimos_30_dias'] }}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Últimos 7 días:</strong>
                            <span class="badge bg-secondary">{{ $stats['ultimos_7_dias'] }}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Hoy:</strong>
                            <span class="badge bg-secondary">{{ $stats['hoy'] }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3">Información Adicional</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Promedio diario (últimos 30 días):</strong>
                            <span class="badge bg-info">{{ round($stats['ultimos_30_dias'] / 30, 2) }}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Densidad de actividad:</strong>
                            <span class="badge bg-info">
                                @php
                                    $densidad = $stats['hoy'] > 0 ? 'Alta' : ($stats['ultimos_7_dias'] > 0 ? 'Media' : 'Baja');
                                @endphp
                                {{ $densidad }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('administracion.bitacora.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<style>
.card {
    border: none;
    border-top: 3px solid #dee2e6;
}

.card-header {
    border-radius: 0;
}

.progress {
    background-color: #e9ecef;
}

.display-6 {
    font-weight: bold;
    color: #0d6efd;
}
</style>
@endsection
