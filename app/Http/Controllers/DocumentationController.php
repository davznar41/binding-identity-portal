<?php

namespace App\Http\Controllers;

use App\Documentation\Repository;
use App\Documentation\Urls;
use Illuminate\Http\Response;
use Illuminate\View\View;

final class DocumentationController
{
    public function __construct(private readonly Repository $documents, private readonly Urls $urls) {}

    private function shared(): array
    {
        return ['navigation' => $this->documents->navigation(), 'urls' => $this->urls, 'currentId' => null];
    }

    public function index(): View
    {
        return view('documentation.index', $this->shared() + [
            'title' => 'Documentación de Binding Identity',
            'description' => 'Documentación de las funcionalidades de Binding Identity.',
        ]);
    }

    public function show(string $slug): View|Response
    {
        $article = $this->documents->find($slug);
        if ($article === null) {
            return $this->notFound();
        }
        $ordered = $this->documents->ordered();
        $position = array_search($slug, array_map(fn ($item) => $item->id, $ordered), true);
        $node = collect($this->documents->navigation())->firstWhere('id', $slug);

        return view('documentation.article', array_merge($this->shared(), [
            'title' => $article->title, 'description' => $article->description,
            'currentId' => $article->id, 'article' => $article,
            'parent' => $this->documents->parentOf($slug),
            'children' => $node['children'] ?? [],
            'previous' => $ordered[$position - 1] ?? null, 'next' => $ordered[$position + 1] ?? null,
        ]));
    }

    public function notFound(): Response
    {
        return response()->view('errors.404', $this->shared() + [
            'title' => 'Página no encontrada', 'description' => 'Vuelve al índice de documentación de Binding Identity.',
        ], 404);
    }
}
