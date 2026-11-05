<?php

declare(strict_types=1);

namespace App\Documentation;

use Illuminate\Http\Request;
use InvalidArgumentException;

final readonly class Urls
{
    public function __construct(private Request $request) {}

    public static function join(string $base, string $path): string
    {
        $prefix = trim($base, '/');

        return ($prefix === '' ? '' : '/'.$prefix).'/'.ltrim($path, '/');
    }

    public function index(): string
    {
        return self::join($this->request->getBaseUrl(), '/');
    }

    public function article(string $id): string
    {
        if (! preg_match('/\A[a-z0-9-]+(?:\/[a-z0-9-]+)?\z/', $id)) {
            throw new InvalidArgumentException('ID de ruta inválido.');
        }

        return self::join($this->request->getBaseUrl(), '/docs/'.$id.'/');
    }

    public function asset(string $path): string
    {
        if (str_contains($path, '..') || ! preg_match('/\A[a-zA-Z0-9_\/.\-]+\z/', $path)) {
            throw new InvalidArgumentException('El recurso debe ser local.');
        }

        return self::join($this->request->getBaseUrl(), $path);
    }

    public function icon(string $id): ?string
    {
        $icon = config('documentation.icons', [])[$id] ?? null;
        if ($icon === null) {
            return null;
        }
        if (! is_string($icon) || ! preg_match('/\Aassets\/icons\/[a-zA-Z0-9_\/-]+\.svg\z/', $icon)) {
            throw new InvalidArgumentException('Solo se admiten SVG locales aprobados en assets/icons.');
        }

        return $this->asset($icon);
    }
}
