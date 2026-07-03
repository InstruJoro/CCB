# MVP — Módulo de Recolección y Clasificación de Incidentes · CCB

Implementación del módulo definido en la **Propuesta Técnica V2.0** para el portal del
Centro de Ciberseguridad de Bolivia (https://ccbol.org). Stack según sección 11 de la
propuesta: **Laravel 11 + Blade + Bootstrap 5 + PostgreSQL**.

## Contenido

| Carpeta | Contenido |
|---|---|
| `database/migrations/` | Modelo de datos V2 (sección 12): incidentes, seguimientos, adjuntos, reclasificaciones, roles |
| `app/Enums/` | Catálogo de estados (sección 5.2) y taxonomía de incidentes (sección 6) con transiciones y priorización |
| `app/Http/Requests/` | Validación en servidor de todos los campos (sección 8.3) |
| `app/Rules/Recaptcha.php` | Verificación de reCAPTCHA en servidor (sección 8.1) |
| `app/Services/` | Código de seguimiento CCB-YYYYMMDD-NNNN (sección 10) y almacenamiento seguro de evidencias (sección 8.2) |
| `app/Http/Controllers/Publico/` | Formulario de reporte y consulta de estado sin cuenta |
| `app/Http/Controllers/Admin/` | Bandeja, cambio de estados con historial, reclasificación con justificación |
| `resources/views/` | Vistas Blade + Bootstrap (formulario por bloques, confirmación, consulta, panel) |

## Instalación

Este paquete contiene **el módulo**, que se instala sobre un esqueleto Laravel 11:

```bash
# 1. Crear el proyecto base con autenticación
composer create-project laravel/laravel ccb-portal
cd ccb-portal
composer require laravel/breeze --dev
php artisan breeze:install blade

# 2. Copiar el contenido de este paquete sobre el proyecto
#    (app/, database/, resources/, routes/web.php se fusiona, config/incidentes.php)

# 3. Agregar el disco privado de config/filesystems_fragmento.php
#    al arreglo 'disks' de config/filesystems.php

# 4. Configurar .env a partir de .env.example (BD, SMTP, reCAPTCHA)

# 5. Migrar y sembrar
php artisan migrate
php artisan db:seed --class=UsuarioInicialSeeder
```

## Mapa de controles de seguridad implementados

| Amenaza | Control | Dónde |
|---|---|---|
| Inyección SQL | Eloquent ORM con consultas preparadas; cero SQL concatenado | Todos los modelos y controladores |
| XSS | Escape automático de Blade `{{ }}` en toda salida de datos del usuario | Todas las vistas |
| CSRF | Token `@csrf` en todos los formularios (middleware `web` de Laravel) | Vistas + rutas |
| Envíos automatizados / abuso | reCAPTCHA verificado **en servidor** + rate limiting por IP (5 reportes/hora, 10 consultas/min) | `Rules/Recaptcha.php`, `routes/web.php` |
| Carga de archivos maliciosos | Lista blanca de MIME **real** (finfo) y extensiones; 10 MB máx.; 3 archivos máx.; nombre aleatorio en disco; hash SHA-256 | `Services/AlmacenAdjuntos.php` |
| Acceso directo a evidencias | Disco privado fuera de `public/`, sin URL; descarga solo autenticada con `Content-Disposition: attachment` | `config/filesystems_fragmento.php`, `CasoController` |
| Path traversal / doble extensión | El nombre original del archivo nunca toca el filesystem; UUID + extensión validada | `AlmacenAdjuntos::guardar()` |
| Enumeración de casos ajenos | La consulta pública exige código **+ correo**; mensaje de error uniforme; rate limiting | `ConsultaController` |
| Asignación masiva | Campos de gestión interna (`estado`, `severidad`, `responsable_id`) fuera de `$fillable` | `Models/Incidente.php` |
| Exposición de datos personales | `correo`, `telefono`, `ip_origen` en `$hidden`; datos de contacto solo visibles en panel autenticado | `Models/Incidente.php` |
| Escalada entre estados | Máquina de estados: solo transiciones del catálogo formal (5.2) | `Enums/EstadoIncidente.php` |
| Contraseñas | Hash bcrypt; seeder genera contraseña aleatoria mostrada una sola vez | `UsuarioInicialSeeder` |
| Fuga por errores | `APP_DEBUG=false`; cookies `Secure`, `HttpOnly`, `SameSite` | `.env.example` |

## Trazabilidad funcional (V2)

- **Sección 5.1**: los 4 bloques del formulario están implementados campo por campo, incluidos título obligatorio, organización reportante/afectada separadas y ciudad del incidente.
- **Sección 5.2**: los 8 estados con transiciones controladas.
- **Sección 6**: taxonomía de 10 tipos con "Otro (especificar)" y detalle obligatorio condicional.
- **Sección 7**: priorización automática cuando el incidente sigue activo o el tipo es crítico.
- **Sección 8**: CAPTCHA, controles de adjuntos, sanitización por validación + escape, rate limiting, CSRF.
- **Sección 10**: código único, pantalla de confirmación, correo automático, consulta sin cuenta.

## Pendientes conocidos (fuera del alcance MVP)

- Escaneo antimalware de adjuntos en servidor (integrar ClamAV vía `clamd` en `AlmacenAdjuntos` antes de persistir).
- Panel de reportes estadísticos (el campo `ciudad_incidente` ya queda registrado con ese fin).
- Notificación al equipo interno por correo/panel al recibir un caso (hook disponible en `ReporteController::store`).
- Política de retención automatizada de evidencias (12 meses según `config/incidentes.php`).
