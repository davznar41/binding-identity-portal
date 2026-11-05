@extends('layouts.documentation')
@section('content')
    <p class="eyebrow">Error 404</p>
    <h1>Página no encontrada</h1>
    <p class="home-lead">Esta dirección no corresponde a un artículo de la documentación.</p>
    <a class="primary-link" href="{{ $urls->index() }}">Volver al índice <span aria-hidden="true">→</span></a>
@endsection
