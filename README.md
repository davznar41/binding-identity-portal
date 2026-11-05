# Binding Identity — Portal de documentación

Portal de consulta de las funcionalidades de Binding Identity, con artículos Markdown, vistas Blade, navegación jerárquica y recursos corporativos locales.

## Tecnologías

Laravel 11.57.1, PHP 8.3, Blade, Bootstrap 5.3.8 y Markdown procesado con League CommonMark. La preparación de CSS y JavaScript utiliza scripts de Node.js definidos en `package.json`.

## Características

- 21 artículos organizados en 15 apartados principales y seis subapartados de Bi Authority Reporting.
- Índice general en `/` y artículos en `/docs/{id}/`, incluidos identificadores jerárquicos.
- Componentes Blade para cabecera, menú lateral, migas de pan y navegación anterior/siguiente.
- Interfaz responsive con estilos corporativos, fuentes locales y navegación disponible sin JavaScript.

El portal es de lectura y no requiere base de datos ni autenticación.

## Requisitos

- PHP 8.3 con `curl`, `dom`, `fileinfo`, `intl`, `mbstring`, `openssl`, `xml`, `xmlwriter` y `zip`, además de las extensiones estándar comprobadas por Composer.
- Composer 2.11.1, utilizado también en el workflow de CI.
- Node.js según `.nvmrc` y npm.
- Chrome para las pruebas locales de navegador.

## Instalación

Desde la raíz del proyecto, con PHP y Composer en `PATH`:

```sh
composer check-platform-reqs --lock
composer install
php -r "is_file('.env') || copy('.env.example', '.env');"
php artisan key:generate
npm ci
npm run build
```

La clave se genera en `.env`, excluido de Git junto con dependencias, cachés y compilados. `npm run build` prepara Bootstrap, CSS y JavaScript en `public/build/`, desde donde se sirven sin un proceso de desarrollo adicional.

## Ejecución local

```sh
php -S 127.0.0.1:8080 -t public scripts/router.php
```

Acceso: [http://127.0.0.1:8080/](http://127.0.0.1:8080/). El servidor integrado de PHP está destinado al desarrollo local y debe permanecer enlazado a `127.0.0.1`.

## Estructura del proyecto

| Ubicación | Responsabilidad |
|---|---|
| `app/Documentation/` | Lectura y validación documental, renderizado Markdown y generación de URLs |
| `config/documentation.php` | Orden de navegación, adaptador visual y registro SVG opcional |
| `routes/`, `app/Http/Controllers/` | Resolución del índice, artículos y respuestas 404 |
| `resources/content/docs/` | Artículos y metadatos YAML |
| `resources/views/` | Layout, componentes Blade y adaptador Bootstrap |
| `resources/css/`, `resources/js/`, `public/assets/` | Estilos, comportamiento y recursos corporativos |
| `tests/`, `scripts/`, `.github/workflows/` | Pruebas, preparación de recursos e integración continua |

El campo `id` determina la URL del artículo. Los cambios documentales requieren revisar navegación, fixtures y contratos de integridad. El renderizador escapa HTML y rechaza enlaces inseguros. El adaptador visual y los SVG opcionales se describen en la [guía de integración visual](docs/INTEGRACION-VISUAL.md).

## Desarrollo y pruebas

| Comando | Comprobación |
|---|---|
| `composer validate` | Manifiesto y coherencia del lockfile |
| `composer check-platform-reqs` | Requisitos de PHP y extensiones |
| `composer check` | Contenidos y compilación de vistas Blade |
| `composer lint` | Estilo PHP con Laravel Pint |
| `composer test` | Pruebas PHPUnit |
| `npm run check` | Tipos de las pruebas y del JavaScript del menú |
| `npm run lint` | Sintaxis JavaScript con Node.js |
| `npm run build` | Preparación de recursos locales |
| `npm test` | Playwright, axe-core, navegación, responsive y contraste renderizado |
| `npm run test:contrast` | Contraste de los colores definidos en los estilos |
| `npm run test:base` | Navegación y recursos bajo `/documentacion/` |
| `composer audit --locked` y `npm audit` | Auditorías de dependencias |

Ejecutar las suites de navegador en serie: principal en `8080` y prefijo en `8081`. Solo puede reutilizarse un servidor conocido de la misma aplicación en `8080`; `8081` debe estar libre. `PHP_BINARY` selecciona PHP para estos scripts.

Las pruebas locales utilizan Chrome; la CI instala Chromium y sus dependencias. Informes y capturas: `verification/`.

## Accesibilidad

Se implementan regiones semánticas, enlace de salto, navegación por teclado, foco visible, estados ARIA, migas de pan, diseño responsive y estilos para movimiento reducido, colores forzados e impresión.

La evaluación manual con lectores de pantalla, zoom nativo y dispositivos sigue pendiente. El alcance se detalla en [Accesibilidad](docs/ACCESIBILIDAD.md). Las pruebas automáticas no acreditan conformidad completa WCAG o UNE.

## Seguridad y limitaciones

Laravel 11.57.1 presenta avisos conocidos y está fuera de soporte de seguridad. Su uso se limita a desarrollo local y CI aislada; el despliegue en producción requiere revisión y remediación.

Las excepciones de instalación no silencian avisos. Las auditorías conservan sus códigos de salida y pueden hacer fallar la CI aunque las pruebas funcionales pasen. Véase [Seguridad](docs/SEGURIDAD-LOCAL.md).

## Recursos y licencias

Bootstrap utiliza licencia MIT, copiada durante la compilación a `public/build/BOOTSTRAP-LICENSE.txt`. Inter y Montserrat incluyen licencias SIL Open Font License 1.1 en `public/assets/fonts/`. El proyecto no incluye una licencia general.

AdminKit PRO no está integrado; requiere sus recursos y licencia. Véase la [guía de integración visual](docs/INTEGRACION-VISUAL.md).
