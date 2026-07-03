@extends('layouts.app')
@section('titulo', 'Reportar incidente')

@section('contenido')
<div class="row justify-content-center">
<div class="col-lg-9">
    <h1 class="h3 mb-1">Reportar un incidente de ciberseguridad</h1>
    <p class="text-muted mb-4">Complete los campos marcados con *. Recibirá un código de seguimiento al finalizar.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revise los siguientes campos:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- enctype para adjuntos; @csrf previene falsificación de solicitud --}}
    <form method="POST" action="{{ route('reporte.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- BLOQUE 1 --}}
        <h2 class="h5 bloque-titulo mt-4">1. Identificación del reportante</h2>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre completo *</label>
                <input type="text" name="nombre_reportante" maxlength="120" required
                       class="form-control" value="{{ old('nombre_reportante') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo electrónico *</label>
                <input type="email" name="correo" maxlength="150" required
                       class="form-control" value="{{ old('correo') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Teléfono de contacto</label>
                <input type="text" name="telefono" maxlength="30"
                       class="form-control" value="{{ old('telefono') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Tipo de usuario *</label>
                <select name="tipo_usuario" class="form-select" required>
                    <option value="">Seleccione…</option>
                    @foreach (['ciudadano' => 'Ciudadano', 'empresa' => 'Empresa', 'institucion' => 'Institución', 'miembro' => 'Miembro', 'otro' => 'Otro'] as $valor => $texto)
                        <option value="{{ $valor }}" @selected(old('tipo_usuario') === $valor)>{{ $texto }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Organización reportante</label>
                <input type="text" name="organizacion_reportante" maxlength="150"
                       class="form-control" value="{{ old('organizacion_reportante') }}">
                <div class="form-text">Entidad desde la cual reporta, si aplica.</div>
            </div>
        </div>

        {{-- BLOQUE 2 --}}
        <h2 class="h5 bloque-titulo mt-4">2. Datos del incidente</h2>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Título del incidente *</label>
                <input type="text" name="titulo" maxlength="150" required
                       class="form-control" placeholder="Resumen del evento en una línea"
                       value="{{ old('titulo') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Tipo de incidente *</label>
                <select name="tipo_incidente" id="tipo_incidente" class="form-select" required>
                    <option value="">Seleccione…</option>
                    @foreach ($tipos as $tipo)
                        <option value="{{ $tipo->value }}" @selected(old('tipo_incidente') === $tipo->value)>
                            {{ $tipo->etiqueta() }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6" id="grupo_detalle" hidden>
                <label class="form-label">Especifique el tipo de incidente *</label>
                <input type="text" name="tipo_incidente_detalle" maxlength="200"
                       class="form-control" value="{{ old('tipo_incidente_detalle') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Fecha y hora aproximada *</label>
                <input type="datetime-local" name="fecha_ocurrencia" required
                       class="form-control" value="{{ old('fecha_ocurrencia') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">¿El incidente sigue ocurriendo? *</label>
                <select name="sigue_activo" class="form-select" required>
                    <option value="">Seleccione…</option>
                    <option value="1" @selected(old('sigue_activo') === '1')>Sí</option>
                    <option value="0" @selected(old('sigue_activo') === '0')>No</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Descripción del incidente *</label>
                <textarea name="descripcion" rows="5" minlength="30" maxlength="5000" required
                          class="form-control"
                          placeholder="Relate lo ocurrido con el mayor detalle posible, sin abreviaciones.">{{ old('descripcion') }}</textarea>
            </div>
        </div>

        {{-- BLOQUE 3 --}}
        <h2 class="h5 bloque-titulo mt-4">3. Entidad y activo afectado</h2>
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Organización afectada</label>
                <input type="text" name="organizacion_afectada" maxlength="150"
                       class="form-control" value="{{ old('organizacion_afectada') }}">
                <div class="form-text">Puede ser distinta de la organización reportante.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Ciudad del incidente</label>
                <input type="text" name="ciudad_incidente" maxlength="100"
                       class="form-control" value="{{ old('ciudad_incidente') }}">
                <div class="form-text">Lugar donde ocurrió el evento.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Activo o servicio afectado *</label>
                <input type="text" name="activo_afectado" maxlength="200" required
                       class="form-control" placeholder="Cuenta, sitio, sistema, equipo…"
                       value="{{ old('activo_afectado') }}">
            </div>
        </div>

        {{-- BLOQUE 4 --}}
        <h2 class="h5 bloque-titulo mt-4">4. Soporte del caso</h2>
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Evidencia adjunta</label>
                <input type="file" name="evidencias[]" multiple class="form-control"
                       accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx">
                <div class="form-text">Hasta 3 archivos. Tipos permitidos: JPG, PNG, PDF, DOCX, XLSX. Máximo 10 MB cada uno.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Nivel de urgencia percibido</label>
                <select name="urgencia_reportante" class="form-select">
                    <option value="">Seleccione…</option>
                    <option value="baja" @selected(old('urgencia_reportante') === 'baja')>Baja</option>
                    <option value="media" @selected(old('urgencia_reportante') === 'media')>Media</option>
                    <option value="alta" @selected(old('urgencia_reportante') === 'alta')>Alta</option>
                </select>
            </div>
        </div>

        {{-- CAPTCHA --}}
        <div class="mt-4">
            <div class="g-recaptcha" data-sitekey="{{ config('incidentes.recaptcha.site_key') }}"></div>
        </div>

        <button type="submit" class="btn btn-ccb btn-lg mt-4">Enviar reporte</button>
    </form>
</div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    // Mostrar el campo de detalle solo cuando tipo = otro
    const selector = document.getElementById('tipo_incidente');
    const grupo = document.getElementById('grupo_detalle');
    function alternarDetalle() { grupo.hidden = selector.value !== 'otro'; }
    selector.addEventListener('change', alternarDetalle);
    alternarDetalle();
</script>
@endsection
