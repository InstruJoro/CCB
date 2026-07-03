<?php

/*
|--------------------------------------------------------------------------
| Configuración del módulo de incidentes CCB
|--------------------------------------------------------------------------
| Controles de seguridad de adjuntos según Propuesta V2, sección 8.2.
| Centralizar aquí evita valores mágicos dispersos en el código.
*/

return [

    'adjuntos' => [
        // Lista blanca (nunca lista negra): solo estos MIME reales se aceptan
        'mimes_permitidos' => [
            'image/jpeg',
            'image/png',
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',       // .xlsx
        ],
        'extensiones_permitidas' => ['jpg', 'jpeg', 'png', 'pdf', 'docx', 'xlsx'],
        'tamanio_maximo_kb'      => 10240, // 10 MB por archivo
        'maximo_archivos'        => 3,
        // Disco privado: fuera de public/, sin URL directa (V2 sección 8.2)
        'disco'                  => 'adjuntos_privados',
    ],

    'rate_limit' => [
        // Envíos de formulario por IP por hora (V2 sección 8.3)
        'reportes_por_hora' => 5,
        // Consultas de estado por IP por minuto (evita enumeración de códigos)
        'consultas_por_minuto' => 10,
    ],

    'recaptcha' => [
        'site_key'   => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'umbral_v3'  => 0.5,
    ],

    'retencion_evidencias_meses' => 12,
];
