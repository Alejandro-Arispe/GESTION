@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-5">
                <i class="fas fa-history"></i> Bitácora del Sistema
            </h1>
            <p class="text-muted">Registro de todas las acciones realizadas en el sistema</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('administracion.bitacora.estadisticas') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Estadísticas
            </a>
            <a href="{{ route('administracion.bitacora.exportar') }}" class="btn btn-success">
                <i class="fas fa-download"></i> Exportar CSV
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-filter"></i> Filtros</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('administracion.bitacora.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><strong>Usuario</strong></label>
                    <select name="usuario" class="form-select">
                        <option value="">-- Todos los usuarios --</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id_usuario }}" {{ $filtros['usuario'] == $usuario->id_usuario ? 'selected' : '' }}>
                                {{ $usuario->nombre }} {{ $usuario->apellido }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><strong>Fecha</strong></label>
                    <input type="date" name="fecha" class="form-control" value="{{ $filtros['fecha'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><strong>Tabla Afectada</strong></label>
                    <input type="text" name="tabla" class="form-control" placeholder="ej: usuario" value="{{ $filtros['tabla'] }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><strong>Búsqueda</strong></label>
                    <input type="text" name="q" class="form-control" placeholder="Acción, IP, etc..." value="{{ $filtros['q'] }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('administracion.bitacora.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Bitácora -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Registros
                <span class="badge bg-light text-primary float-end">{{ $bitacoras->total() }} total</span>
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Tabla</th>
                        <th>IP Origen</th>
                        <th>Navegador</th>
                        <th>Fecha/Hora</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bitacoras as $bitacora)
                        <tr>
                            <td>
                                @if($bitacora->usuario)
                                    <strong>{{ $bitacora->usuario->nombre ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $bitacora->usuario->apellido ?? '' }}</small>
                                @else
                                    <span class="text-muted">Usuario eliminado</span>
                                @endif
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1">{{ $bitacora->accion }}</code>
                            </td>
                            <td>
                                @if($bitacora->tabla_afectada)
                                    <span class="badge bg-secondary">{{ $bitacora->tabla_afectada }}</span>
                                    @if($bitacora->registro_id)
                                        <br><small class="text-muted">ID: {{ $bitacora->registro_id }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="font-monospace">{{ $bitacora->ip_origen }}</small>
                            </td>
                            <td>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;" title="{{ $bitacora->navegador ?? 'N/A' }}">
                                    {{ substr($bitacora->navegador ?? 'N/A', 0, 50) }}...
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $bitacora->created_at->format('Y-m-d H:i:s') }}
                                    <br><small class="text-secondary">{{ $bitacora->created_at->diffForHumans() }}</small>
                                </small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('administracion.bitacora.show', $bitacora->id_bitacora) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox"></i> No hay registros de bitácora
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $bitacoras->links() }}
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
}

.font-monospace {
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
}
</style>
@endsection
