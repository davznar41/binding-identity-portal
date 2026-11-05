<?php

use App\Documentation\Repository;
use Illuminate\Support\Facades\Artisan;

Artisan::command('docs:check', function (Repository $documents): void {
    $all = $documents->all();
    $navigation = $documents->navigation();
    $this->info(count($all).' artículos Markdown; '.count($navigation).' apartados principales; '.array_sum(array_map(fn ($node) => count($node['children']), $navigation)).' subapartados.');
})->purpose('Validar metadatos, contenido y contrato de navegación documental');
