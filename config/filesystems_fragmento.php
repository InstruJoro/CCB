<?php

/*
 * Agregar este disco al arreglo 'disks' de config/filesystems.php.
 * Clave del control (V2, sección 8.2): la raíz está en storage/app,
 * FUERA de public/. Los archivos no tienen URL directa; solo se
 * sirven vía CasoController::descargarAdjunto con autenticación.
 */

return [
    'adjuntos_privados' => [
        'driver' => 'local',
        'root'   => storage_path('app/evidencias_privadas'),
        'serve'  => false,
        'throw'  => false,
    ],
];
