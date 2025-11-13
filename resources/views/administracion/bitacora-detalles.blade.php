@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6">
                <i class="fas fa-magnifying-glass"></i> Detalles de Acción
            </h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('administracion.bitacora.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información General -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Información General</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4"><strong>ID:</strong></div>
                        <div class="col-8"><code>{{ $bitacora->id_bitacora }}</code></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Usuario:</strong></div>
                        <div class="col-8">
                            @if($bitacora->usuario)
                                {{ $bitacora->usuario->nombre }} {{ $bitacora->usuario->apellido }}
                            @else
                                <span class="text-muted">Usuario eliminado</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Acción:</strong></div>
                        <div class="col-8"><code class="bg-light px-2 py-1">{{ $bitacora->accion }}</code></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Fecha/Hora:</strong></div>
                        <div class="col-8">
                            <small class="text-muted">
                                {{ $bitacora->created_at->format('d/m/Y H:i:s') }}
                                <br><small>{{ $bitacora->created_at->diffForHumans() }}</small>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Red -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Información de Red</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4"><strong>IP Origen:</strong></div>
                        <div class="col-8"><code class="font-monospace">{{ $bitacora->ip_origen }}</code></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Navegador:</strong></div>
                        <div class="col-8"><small class="text-muted">{{ $bitacora->navegador ?? 'No registrado' }}</small></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tabla Afectada -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">Elemento Afectado</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4"><strong>Tabla:</strong></div>
                        <div class="col-8">
                            @if($bitacora->tabla_afectada)
                                <span class="badge bg-secondary">{{ $bitacora->tabla_afectada }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4"><strong>Registro ID:</strong></div>
                        <div class="col-8">
                            @if($bitacora->registro_id)
                                <code>{{ $bitacora->registro_id }}</code>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Descripción/Datos -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0">Datos Enviados</h6>
        </div>
        <div class="card-body">
            @if(is_array($descripcion))
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Campo</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($descripcion as $campo => $valor)
                                <tr>
                                    <td><strong>{{ $campo }}</strong></td>
                                    <td>
                                        @if(is_array($valor) || is_object($valor))
                                            <pre class="mb-0 bg-light p-2"><code>{{ json_encode($valor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                        @else
                                            <code>{{ $valor }}</code>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <pre class="mb-0 bg-light p-3"><code>{{ $descripcion }}</code></pre>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('administracion.bitacora.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<style>
.font-monospace {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
}

pre {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
}
</style>
@endsection
