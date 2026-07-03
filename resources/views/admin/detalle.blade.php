@extends('layouts.app')
@section('titulo', $incidente->codigo)

@section('contenido')
@if (session('ok'))
    <div class="alert alert-success">{{ session('ok') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h1 class="h4 mb-1">{{ $incidente->titulo }}</h1>
        <span class="font-monospace text-muted">{{ $incidente->codigo }}</span>
        @if ($incidente->prioritario)
            <span class="badge text-bg-warning ms-2">Atención prioritaria</span>
        @endif
    </div>
    <span class="badge text-bg-primary fs-6">{{ $incidente->estado->etiqueta() }}</span>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm mb-4">
            <div class="card-header">Datos del reporte</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Reportante</dt><dd class="col-sm-8">{{ $incidente->nombre_reportante }} ({{ $incidente->tipo_usuario }})</dd>
                    <dt class="col-sm-4">Org. reportante</dt><dd class="col-sm-8">{{ $incidente->organizacion_reportante ?? '—' }}</dd>
                    <dt class="col-sm-4">Org. afectada</dt><dd class="col-sm-8">{{ $incidente->organizacion_afectada ?? '—' }}</dd>
                    <dt class="col-sm-4">Ciudad del incidente</dt><dd class="col-sm-8">{{ $incidente->ciudad_incidente ?? '—' }}</dd>
                    <dt class="col-sm-4">Tipo</dt>
                    <dd class="col-sm-8">
                        {{ $incidente->tipo_incidente->etiqueta() }}
                        @if ($incidente->tipo_incidente_detalle)
                            — {{ $incidente->tipo_incidente_detalle }}
                        @endif
                    </dd>
                    <dt class="col-sm-4">Activo afectado</dt><dd class="col-sm-8">{{ $incidente->activo_afectado }}</dd>
                    <dt class="col-sm-4">Ocurrencia</dt><dd class="col-sm-8">{{ $incidente->fecha_ocurrencia->format('d/m/Y H:i') }}</dd>
                    <dt class="col-sm-4">¿Sigue activo?</dt><dd class="col-sm-8">{{ $incidente->sigue_activo ? 'Sí' : 'No' }}</dd>
                    <dt class="col-sm-4">Urgencia percibida</dt><dd class="col-sm-8">{{ $incidente->urgencia_reportante ? ucfirst($incidente->urgencia_reportante) : '—' }}</dd>
                </dl>
                <hr>
                {{-- {{ }} escapa HTML: la descripción del reportante jamás se renderiza como código --}}
                <p class="mb-0" style="white-space: pre-wrap">{{ $incidente->descripcion }}</p>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">Evidencias ({{ $incidente->adjuntos->count() }})</div>
            <ul class="list-group list-group-flush">
                @forelse ($incidente->adjuntos as $adjunto)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            {{ $adjunto->nombre_original }}
                            <small class="text-muted d-block">
                                {{ $adjunto->tipo_mime }} · {{ number_format($adjunto->tamanio_bytes / 1024, 0) }} KB
                                · SHA-256: <span class="font-monospace">{{ substr($adjunto->hash_sha256, 0, 16) }}…</span>
                            </small>
                        </div>
                        <a href="{{ route('admin.adjunto.descargar', $adjunto) }}" class="btn btn-sm btn-outline-secondary">Descargar</a>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Sin evidencias adjuntas.</li>
                @endforelse
            </ul>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">Historial de seguimiento</div>
            <ul class="list-group list-group-flush">
                @foreach ($incidente->seguimientos as $seg)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <strong>{{ ucfirst(str_replace('_', ' ', $seg->accion)) }}</strong>
                            <small class="text-muted">{{ $seg->created_at->format('d/m/Y H:i') }} · {{ $seg->usuario?->name ?? 'Sistema' }}</small>
                        </div>
                        @if ($seg->estado_anterior)
                            <small class="text-muted">{{ $seg->estado_anterior }} → {{ $seg->estado_resultante }}</small>
                        @endif
                        <p class="mb-0">{{ $seg->detalle }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm mb-4">
            <div class="card-header">Cambiar estado</div>
            <div class="card-body">
                @if (count($transiciones))
                    <form method="POST" action="{{ route('admin.caso.estado', $incidente) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nuevo estado</label>
                            <select name="estado" class="form-select" required>
                                @foreach ($transiciones as $t)
                                    <option value="{{ $t->value }}">{{ $t->etiqueta() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Severidad</label>
                            <select name="severidad" class="form-select">
                                <option value="">Sin cambio</option>
                                @foreach (['baja', 'media', 'alta', 'critica'] as $s)
                                    <option value="{{ $s }}" @selected($incidente->severidad === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observación *</label>
                            <textarea name="observacion" class="form-control" rows="3" required maxlength="1000"></textarea>
                        </div>
                        <button class="btn btn-ccb">Actualizar estado</button>
                    </form>
                @else
                    <p class="text-muted mb-0">El caso está en un estado final ({{ $incidente->estado->etiqueta() }}). No admite transiciones.</p>
                @endif
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">Reclasificar tipo de incidente</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.caso.reclasificar', $incidente) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nuevo tipo</label>
                        <select name="tipo_nuevo" class="form-select" required>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->value }}" @selected($incidente->tipo_incidente === $tipo)>
                                    {{ $tipo->etiqueta() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Justificación * <small class="text-muted">(mínimo 15 caracteres)</small></label>
                        <textarea name="justificacion" class="form-control" rows="3" required minlength="15" maxlength="1000"></textarea>
                    </div>
                    <button class="btn btn-outline-primary">Reclasificar</button>
                </form>

                @if ($incidente->reclasificaciones->count())
                    <hr>
                    <h3 class="h6">Historial de reclasificaciones</h3>
                    @foreach ($incidente->reclasificaciones as $rec)
                        <small class="d-block text-muted mb-2">
                            {{ $rec->created_at->format('d/m/Y H:i') }} · {{ $rec->usuario->name }}:
                            {{ $rec->tipo_anterior }} → {{ $rec->tipo_nuevo }} — {{ $rec->justificacion }}
                        </small>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
