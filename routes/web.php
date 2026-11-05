<?php

use App\Http\Controllers\DocumentationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DocumentationController::class, 'index'])->name('documentation.index');
Route::get('/docs/{slug}', [DocumentationController::class, 'show'])
    ->where('slug', '[a-z0-9-]+(?:/[a-z0-9-]+)?')->name('documentation.article');
Route::fallback([DocumentationController::class, 'notFound']);
