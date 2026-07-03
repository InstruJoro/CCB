<p>Estimado(a) {{ $incidente->nombre_reportante }},</p>

<p>Su reporte fue recibido por el Centro de Ciberseguridad de Bolivia.</p>

<ul>
    <li><strong>Código de seguimiento:</strong> {{ $incidente->codigo }}</li>
    <li><strong>Título:</strong> {{ $incidente->titulo }}</li>
    <li><strong>Fecha y hora de recepción:</strong> {{ $incidente->fecha_reporte->format('d/m/Y H:i') }}</li>
</ul>

<p>Conserve este código. Puede consultar el estado de su caso en cualquier momento
desde el portal, en la sección <strong>Consultar estado de reporte</strong>, ingresando
el código junto con este correo electrónico.</p>

<p>Centro de Ciberseguridad de Bolivia — https://ccbol.org</p>
