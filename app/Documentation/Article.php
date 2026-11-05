<?php

declare(strict_types=1);

namespace App\Documentation;

final readonly class Article
{
    public function __construct(
        public string $id,
        public string $title,
        public string $body,
        public string $html,
        public string $description,
        public ?string $example,
    ) {}
}
