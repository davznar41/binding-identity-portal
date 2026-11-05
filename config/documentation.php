<?php

return [
    'content_path' => resource_path('content/docs'),
    'primary' => [
        'binding-technology', 'id-electronic-reading', 'id-validate',
        'customizable-paperless-regcard', 'customizable-guest-check-in',
        'customizable-corporate-image', 'customizable-legal-texts', 'customizable-languages',
        'product-updates', 'pms-integrations', 'custom-fields-from-pms', 'data-analytics',
        'api-integrations', 'junior-check-in', 'authority-reporting',
    ],
    'children' => [
        'authority-reporting/pre-check-in', 'authority-reporting/online-check-in',
        'authority-reporting/auto-check-in', 'authority-reporting/group-check-in',
        'authority-reporting/ubiquity-check-in', 'authority-reporting/offline-group-check-in',
    ],
    // El adaptador visual se selecciona sin alterar la carga documental ni las rutas.
    'presentation' => [
        'head' => 'themes.bootstrap.head',
        'shell' => 'themes.bootstrap.shell',
        'scripts' => 'themes.bootstrap.scripts',
    ],
    // Rutas SVG locales opcionales, asociadas al ID estable de cada artículo.
    'icons' => [],
];
