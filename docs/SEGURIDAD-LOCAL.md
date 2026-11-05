# Seguridad y alcance de uso

## Framework y riesgos

Laravel 11 finalizó su soporte de seguridad el 12 de marzo de 2026 según la [política oficial](https://laravel.com/framework/docs/11.x/releases#support-policy). El proyecto fija `laravel/framework` en 11.57.1 y mantiene avisos conocidos que requieren revisión y remediación antes de un despliegue en producción.

| Registro y referencia | Severidad informada | Versiones afectadas según Composer | Relación con el portal |
|---|---|---|---|
| PKSA-b41t-wbzk-9q51 / CVE-2026-114509 · [TokenGuard](https://github.com/advisories/GHSA-f9x3-7vq9-cph6) | Crítica | `<12.68.0` o `>=13.0.0,<13.29.0` | Bypass con credenciales API no textuales. El portal no incorpora guard API ni autenticación. |
| PKSA-d5tc-s1qs-h781 / CVE-2026-102279 · [Depuración](https://github.com/advisories/GHSA-jh5r-qr3c-85q8) | Baja | `<12.69.0` o `>=13.0.0,<13.30.0` | XSS en información de depuración. La configuración utiliza `APP_DEBUG=false`. |
| PKSA-m5cs-t1y6-qpcs · [URL firmada temporal](https://github.com/advisories/GHSA-crmm-hgp2-wgrp) | Media | `<12.61.1` o `>=13.0.0,<13.12.0` | Confusión de ruta. El portal no genera ni valida URLs firmadas. |
| PKSA-3r5d-mb8f-1qw9 · [Regla email](https://github.com/advisories/GHSA-5vg9-5847-vvmq) | Alta | `<12.60.0` o `>=13.0.0,<=13.9.0` | Inyección CRLF. No hay formularios operativos, envío de correo ni validación de direcciones email. |
| PKSA-mdq4-51ck-6kdq / CVE-2026-48019 · [Registro CRLF adicional](https://github.com/laravel/framework/security/advisories/GHSA-5vg9-5847-vvmq) | Sin valor en este registro | `>=9.0.0,<10.0.0`, `>=10.0.0,<11.0.0`, `>=11.0.0,<12.0.0`, `>=12.0.0,<12.60.0` o `>=13.0.0,<13.10.0` | Mismo GHSA que el registro anterior; se conserva el duplicado y la severidad informada por cada fuente. |

Estos cinco registros corresponden a cuatro vulnerabilidades distintas. Sus rangos incluyen Laravel 11.57.1. La ausencia de las características señaladas reduce exposición dentro del alcance del portal, pero no demuestra que todos los avisos sean irrelevantes ni corrige el framework.

## Excepciones e instalación reproducible

`composer.json` conserva `audit.block-insecure=true` y limita las excepciones a los cinco IDs indicados, con `apply: block`. Permiten instalar la versión fijada, pero los avisos siguen apareciendo en `composer audit --locked` y conservan su código de salida no cero. No hay exclusiones de auditoría por severidad ni supresión de registros duplicados. Véase la [configuración de auditoría de Composer](https://getcomposer.org/doc/06-config.md#audit).

Las justificaciones delimitan el uso a desarrollo local y CI aislada. No sustituyen la remediación. Los archivos lock fijan las dependencias; cualquier cambio de versión o excepción requiere repetir la instalación y las auditorías. `composer validate` puede advertir sobre la ausencia de licencia general y la restricción exacta del framework; estas advertencias no equivalen a los avisos de vulnerabilidad.

## Ejecución y controles

El portal expone rutas GET/HEAD, utiliza `APP_DEBUG=false` y no requiere autenticación, base de datos ni sesiones. Los IDs documentales se resuelven contra una colección validada; el renderizador escapa HTML y rechaza enlaces inseguros. El servidor local permanece enlazado a `127.0.0.1`.

El almacenamiento privado del código, las pruebas CI aisladas y el despliegue de una aplicación son alcances diferentes. La CI levanta servidores de pruebas en loopback y no publica el portal. Superar las pruebas funcionales no acredita seguridad para producción.

GitHub Actions utiliza Composer 2.11.1 en ambos trabajos. Las auditorías de Composer y npm conservan su JSON y código de salida como artefactos, incluso si fallan. npm se ejecuta también cuando falla Composer; no se utiliza `continue-on-error`. Una auditoría fallida mantiene el trabajo y el workflow en estado fallido.

Las claves locales se generan mediante `php artisan key:generate`. `.env`, credenciales, dependencias instaladas, cachés y resultados están excluidos de Git. Los registros de pruebas y los artefactos deben revisarse antes de compartirse; no deben contener tokens, claves ni otros valores sensibles. Ante exposición de un secreto vigente, debe revocarse o rotarse.

## Condiciones previas a producción

Binding Software debe determinar una versión soportada o una remediación revisada, reevaluar las excepciones y repetir las auditorías y pruebas. También deben revisarse la configuración del servidor, los recursos corporativos y sus licencias. No se debe exponer el servidor integrado de PHP mediante interfaces públicas o túneles.

AdminKit PRO no está integrado y no resuelve los avisos del framework. La evaluación de accesibilidad y sus pendientes se describen en [Accesibilidad](ACCESIBILIDAD.md).
