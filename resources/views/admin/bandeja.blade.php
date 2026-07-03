@extends('layouts.app')
@section('titulo', 'Bandeja de casos')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Bandeja de casos</h1>
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-auto">
        <select name="estado" class="form-select" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            @foreach ($estados as $e)
                <option value="{{ $e->value }}" @selected(request('estado') === $e->value)>{{ $e->etiqueta() }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <input type="text" name="q" class="form-control" placeholder="Código o título…" value="{{ request('q') }}">
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Filtrar</button></div>
</form>

<div class="table-responsive">
<table class="table table-hover align-middle bg-white shadow-sm">
    <thead>
        <tr>
            <th>Código</th><th>Título</th><th>Tipo</th><th>Estado</th>
            <th>Severidad</th><th>Recibido</th><th></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($casos as $caso)
            <tr class="{{ $caso->prioritario ? 'table-warning' : '' }}">
                <td class="font-monospace">{{ $caso->codigo }}</td>
                <td>{{ $caso->titulo }}</td>
                <td>{{ $caso->tipo_incidente->etiqueta() }}</td>
                <td><span class="badge text-bg-secondary">{{ $caso->estado->etiqueta() }}</span></td>
                <td>{{ $caso->severidad ? ucfirst($caso->severidad) : '—' }}</td>
                <td>{{ $caso->fecha_reporte->format('d/m/Y H:i') }}</td>
                <td><a href="{{ route('admin.caso.show', $caso) }}" class="btn btn-sm btn-outline-primary">Abrir</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No hay casos que coincidan con el filtro.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $casos->links() }}
@endsection
