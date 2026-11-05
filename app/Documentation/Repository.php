<?php

declare(strict_types=1);

namespace App\Documentation;

use League\CommonMark\CommonMarkConverter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

final class Repository
{
    /** @var array<string, Article>|null */
    private ?array $loaded = null;

    /** @param list<string> $primary @param list<string> $children */
    public function __construct(
        private readonly string $contentPath,
        private readonly array $primary,
        private readonly array $children,
    ) {}

    /** @return array<string, Article> */
    public function all(): array
    {
        if ($this->loaded !== null) {
            return $this->loaded;
        }
        $expected = [...$this->primary, ...$this->children];
        if (count($expected) !== count(array_unique($expected)) || ! is_dir($this->contentPath)) {
            throw new RuntimeException('Configuración documental inválida.');
        }
        $converter = new CommonMarkConverter(['html_input' => 'escape', 'allow_unsafe_links' => false]);
        $articles = [];
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->contentPath)) as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'md') {
                continue;
            }
            $raw = file_get_contents($file->getPathname());
            if ($raw === false || ! preg_match('/\A---\r?\n(.*?)\r?\n---\r?\n(.*)\z/s', $raw, $parts)) {
                throw new RuntimeException('El artículo necesita frontmatter YAML válido.');
            }
            $data = Yaml::parse($parts[1], Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
            if (! is_array($data) || array_diff(array_keys($data), ['id', 'title', 'example']) !== []) {
                throw new RuntimeException('Metadatos documentales no admitidos.');
            }
            $id = $data['id'] ?? null;
            $title = $data['title'] ?? null;
            $example = $data['example'] ?? null;
            if (! is_string($id) || ! preg_match('/\A[a-z0-9-]+(?:\/[a-z0-9-]+)?\z/', $id)
                || ! is_string($title) || trim($title) === ''
                || ($example !== null && ! is_string($example))) {
                throw new RuntimeException('ID, título o ejemplo documental inválido.');
            }
            if (isset($articles[$id])) {
                throw new RuntimeException('Identificador documental duplicado: '.$id);
            }
            $body = str_replace("\r\n", "\n", $parts[2]);
            if (trim($body) === '') {
                throw new RuntimeException('El artículo necesita contenido.');
            }
            $description = trim(preg_split('/\n\s*\n/', trim($body))[0]);
            $articles[$id] = new Article($id, $title, $body, $converter->convert($body)->getContent(), $description, $example);
        }
        if (count($articles) !== count($expected) || array_diff($expected, array_keys($articles)) !== []) {
            throw new RuntimeException('La colección debe coincidir exactamente con el contrato documental.');
        }
        $this->loaded = [];
        foreach ($expected as $id) {
            $this->loaded[$id] = $articles[$id];
        }

        return $this->loaded;
    }

    public function find(string $id): ?Article
    {
        // La búsqueda en el mapa de artículos evita resolver rutas de archivo desde la petición.
        return $this->all()[$id] ?? null;
    }

    /** @return list<array{id:string,title:string,children:array}> */
    public function navigation(): array
    {
        $articles = $this->all();
        $nodes = [];
        foreach ($this->primary as $id) {
            $children = [];
            foreach ($this->children as $childId) {
                if (str_starts_with($childId, $id.'/')) {
                    $children[] = ['id' => $childId, 'title' => $articles[$childId]->title, 'children' => []];
                }
            }
            $nodes[] = ['id' => $id, 'title' => $articles[$id]->title, 'children' => $children];
        }

        return $nodes;
    }

    public function parentOf(string $id): ?Article
    {
        return in_array($id, $this->children, true) ? $this->find(explode('/', $id)[0]) : null;
    }

    /** @return list<Article> */
    public function ordered(): array
    {
        $ordered = [];
        foreach ($this->navigation() as $node) {
            $ordered[] = $this->find($node['id']);
            foreach ($node['children'] as $child) {
                $ordered[] = $this->find($child['id']);
            }
        }

        return $ordered;
    }
}
