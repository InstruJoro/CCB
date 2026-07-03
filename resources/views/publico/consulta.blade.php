@extends('layouts.app')
@section('titulo', 'Consultar estado')

@section('contenido')
<div class="row justify-content-center">
<div class="col-lg-6">
    <h1 class="h3 mb-4">Consultar estado de reporte</h1>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('consulta.buscar') }}" class="card card-body shadow-sm">
        @csrf
        <div class="mb-3">
            <label class="form-label">Código de seguimiento *</label>
            <input type="text" name="codigo" required class="form-control"
                   placeholder="CCB-20260528-0047" value="{{ old('codigo') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Correo con el que reportó *</label>
            <input type="email" name="correo" required class="form-control" value="{{ old('correo') }}">
        </div>
        <button type="submit" class="btn btn-ccb">Consultar</button>
    </form>

    @isset ($resultado)
        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h2 class="h5">{{ $resultado['titulo'] }}</h2>
                <p class="mb-1"><strong>Código:</strong> {{ $resultado['codigo'] }}</p>
                <p class="mb-1"><strong>Recibido:</strong> {{ $resultado['fecha'] }}</p>
                <p class="mb-0"><strong>Estado actual:</strong>
                    <span class="badge text-bg-primary">{{ $resultado['estado'] }}</span>
                </p>
            </div>
        </div>
    @endisset
</div>
</div>
@endsection
