<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description }}">
    <meta name="theme-color" content="#76236c">
    <title>{{ $title }} | Binding Identity · Documentación</title>
    @include(config('documentation.presentation.head'))
</head>
<body>
    <a class="skip-link" href="#main-content">Saltar al contenido principal</a>
    @include('components.header')
    @include(config('documentation.presentation.shell'))
    @include(config('documentation.presentation.scripts'))
</body>
</html>
