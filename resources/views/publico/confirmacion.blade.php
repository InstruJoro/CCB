@extends('layouts.app')
@section('titulo', 'Reporte recibido')

@section('contenido')
<div class="row justify-content-center">
<div class="col-lg-6 text-center">
    <div class="card shadow-sm">
        <div class="card-body p-5">
            <h1 class="h4 mb-3">Su reporte fue recibido</h1>
            <p class="text-muted">Conserve el siguiente código para consultar el estado de su caso:</p>
            <p class="display-6 fw-bold" style="color: var(--ccb-azul)">{{ $codigo }}</p>
            <p class="text-muted">También le enviamos una confirmación al correo registrado.</p>
            <a href="{{ route('consulta.form') }}" class="btn btn-ccb mt-2">Consultar estado de reporte</a>
        </div>
    </div>
</div>
</div>
@endsection
