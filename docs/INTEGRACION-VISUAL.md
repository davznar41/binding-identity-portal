# Presentación y recursos visuales

## Arquitectura

`app/Documentation/` carga contenido y resuelve la navegación con independencia del tema. El controlador prepara artículos, relaciones jerárquicas y paginación. Los componentes de `resources/views/components/` definen la semántica y los estados accesibles; el layout reúne estos componentes.

`config/documentation.php` selecciona las vistas `head`, `shell` y `scripts`. El adaptador activo está en `resources/views/themes/bootstrap/`. Sus ajustes de estilo se limitan a `resources/css/bootstrap-adapter.css`; los tokens, las tipografías y el comportamiento de navegación permanecen separados. `npm run build` prepara los recursos locales de Bootstrap y copia su licencia.

## Integración de AdminKit PRO

AdminKit PRO no está integrado. Su incorporación requiere los archivos, la versión y la licencia correspondientes de Binding Software.

1. Comprobar si el paquete incorpora Bootstrap para evitar cargar versiones duplicadas.
2. Crear un adaptador en `resources/views/themes/adminkit-pro/` con las entradas de recursos, estructura visual y scripts del paquete. Este directorio solo debe incorporarse junto con los recursos disponibles y su licencia.
3. Seleccionar las tres vistas del adaptador en `config/documentation.php` y ajustar los estilos de integración. El repositorio documental, las rutas, el controlador y los componentes semánticos se mantienen independientes del tema.
4. Repetir pruebas funcionales, responsive y de accesibilidad, incluida la evaluación de foco, contraste, teclado y estados ARIA.

## SVG personalizados

El registro `icons` de `config/documentation.php` es opcional y está vacío. Utiliza los IDs del frontmatter. Para incorporar un recurso, guardar el SVG en `public/assets/icons/` y asociar su ruta local al ID del artículo. Una entrada para un archivo existente tendría la forma `'id-validate' => 'assets/icons/id-validate.svg'`.

Antes de incorporarlo:

- Verificar licencia, `viewBox`, proporciones y legibilidad al tamaño de presentación de 24×24 px.
- Revisar y eliminar scripts, manejadores de eventos y referencias a recursos externos.
- Comprobar la presentación con prefijo de ruta, en móvil, escritorio y colores forzados.

`Urls::icon()` comprueba rutas locales `.svg`; no sanitiza el contenido del archivo. `components/icon.blade.php` representa el recurso como decorativo mediante `alt=""` y `aria-hidden="true"`, porque el título del enlace aporta el nombre accesible. Sin registro no se emiten nodos ni se reserva espacio.

Las pruebas comprueban la ausencia de iconos y la resolución de sus metadatos. Cada SVG incorporado requiere evaluación adicional sobre el recurso real.
