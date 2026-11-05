<?php

namespace Tests\Unit;

use App\Documentation\Repository;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class RepositoryTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir().'/binding-docs-test-'.bin2hex(random_bytes(8));
        mkdir($this->directory);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory.'/*.md') as $file) {
            unlink($file);
        }
        rmdir($this->directory);
    }

    private function write(string $name, string $text): void
    {
        file_put_contents($this->directory.'/'.$name.'.md', $text);
    }

    public function test_markdown_is_parsed_without_allowing_raw_html_or_unsafe_links(): void
    {
        $this->write('one', "---\nid: one\ntitle: Original\n---\n## Encabezado\n\n**Texto** <script>alert(1)</script> [Enlace](javascript:alert(1))\n");
        $article = (new Repository($this->directory, ['one'], []))->find('one');
        $this->assertStringContainsString('<h2>Encabezado</h2>', $article->html);
        $this->assertStringContainsString('<strong>Texto</strong>', $article->html);
        $this->assertStringNotContainsString('<script>', $article->html);
        $this->assertStringNotContainsString('href="javascript:', $article->html);
    }

    public function test_duplicate_ids_are_rejected(): void
    {
        foreach (['one', 'two'] as $name) {
            $this->write($name, "---\nid: one\ntitle: Original\n---\nTexto\n");
        }
        $this->expectException(RuntimeException::class);
        (new Repository($this->directory, ['one'], []))->all();
    }

    public function test_missing_article_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);
        (new Repository($this->directory, ['one'], []))->all();
    }

    public function test_unexpected_metadata_is_rejected(): void
    {
        $this->write('one', "---\nid: one\ntitle: Original\nunknown: no\n---\nTexto\n");
        $this->expectException(RuntimeException::class);
        (new Repository($this->directory, ['one'], []))->all();
    }

    public function test_empty_title_is_rejected(): void
    {
        $this->write('one', "---\nid: one\ntitle: ''\n---\nTexto\n");
        $this->expectException(RuntimeException::class);
        (new Repository($this->directory, ['one'], []))->all();
    }
}
