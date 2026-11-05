<?php

return [
    'name' => 'Binding Identity · Documentación',
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://127.0.0.1:8080'),
    'timezone' => 'Europe/Madrid',
    'locale' => 'es',
    'fallback_locale' => 'es',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => ['driver' => 'file'],
];
