<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Reporte de incidentes') — CCB</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --ccb-azul: #0b2545; --ccb-acento: #1d7a5f; }
        body { background: #f4f6f8; }
        .navbar-ccb { background: var(--ccb-azul); }
        .navbar-ccb .navbar-brand, .navbar-ccb .nav-link { color: #fff; }
        .btn-ccb { background: var(--ccb-acento); color: #fff; }
        .btn-ccb:hover { background: #145c47; color: #fff; }
        .bloque-titulo { border-left: 4px solid var(--ccb-acento); padding-left: .75rem; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand navbar-ccb mb-4">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="/">CCB · Centro de Ciberseguridad de Bolivia</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="{{ route('reporte.create') }}">Reportar incidente</a>
            <a class="nav-link" href="{{ route('consulta.form') }}">Consultar estado</a>
        </div>
    </div>
</nav>
<main class="container pb-5">
    @yield('contenido')
</main>
</body>
</html>
