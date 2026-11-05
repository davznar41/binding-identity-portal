# Accesibilidad

El portal utiliza como referencias técnicas UNE-EN 301549:2020, EN 301 549 V3.2.1 y WCAG 2.2 A/AA, con objetivos AAA adicionales. Las comprobaciones del portal no acreditan la accesibilidad de los productos empresariales descritos ni constituyen una certificación o declaración de conformidad completa.

## Mecanismos implementados

- HTML en español con cabecera, navegaciones identificadas, listas, contenido principal y jerarquía de títulos.
- Índice documental y navegación lateral con 15 apartados y seis subapartados; migas de pan y enlaces anterior/siguiente como mecanismos de orientación.
- Logotipo identificativo no enlazado y enlace nativo «Documentación» al índice, con foco de 3 px, objetivo mínimo de 44 px y subrayado en hover, foco y activación.
- Enlace de salto al contenido principal. Controles nativos de menú y desplegable con `aria-expanded`, `aria-controls` y `aria-current`; Escape restaura el foco del menú móvil.
- Enlaces disponibles sin JavaScript. El registro SVG vacío no genera imágenes ni reserva espacio; los iconos decorativos utilizan el título del enlace como nombre accesible.
- Tipografías locales, variantes cromáticas para texto y estados interactivos, diseño responsive y estilos para movimiento reducido, colores forzados e impresión.

## Evaluación automatizada

| Control | Alcance |
|---|---|
| PHPUnit | Contenido, integridad, parser, rutas, 404, prefijos y ausencia de consultas SQL o sesiones |
| Playwright | Índice, artículos, navegación jerárquica, teclado, foco, enlaces, funcionamiento sin JavaScript y recursos bajo `/documentacion/` |
| axe-core | Portada, artículos, 404 y estados móviles; los resultados incompletos requieren revisión |
| Contraste | Parejas de colores de los estilos y combinaciones de texto, fondo y estados renderizados |
| Responsive | Anchuras de 320 a 1440 px, desbordamientos y visibilidad de los controles |
| Capturas | Portada, artículos, apartado padre, subapartados, menú móvil y foco |

Los comandos están documentados en el [README](../README.md). Las suites generan informes en `verification/`. La ausencia de infracciones detectadas por axe-core no verifica todos los criterios; las pruebas de teclado, contraste y reflujo aportan evidencia complementaria. La emulación de preferencias y la ampliación de texto mediante CSS no equivalen a una evaluación manual del navegador y del sistema operativo.

## Verificaciones pendientes

- Zoom nativo al 200 % y 400 %, recorrido humano de teclado y comprobación del orden de lectura.
- Lectores de pantalla: NVDA con Chrome/Firefox y VoiceOver con Safari.
- Alto contraste del sistema, dispositivos táctiles y cambios de orientación en equipos físicos.
- Comprensión de los textos, definiciones de términos especializados y abreviaturas para los objetivos AAA aplicables.
- Evaluación de los SVG definitivos y de cualquier adaptación visual de AdminKit PRO.
- Características de accesibilidad y compatibilidad de Binding Identity, así como la aplicabilidad del capítulo 12 de EN 301 549 a documentación y servicios de apoyo.

AdminKit PRO no está integrado ni evaluado. Los riesgos del framework se documentan en [Seguridad](SEGURIDAD-LOCAL.md); las pruebas de accesibilidad no los resuelven.
