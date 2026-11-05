<?php

namespace Tests\Feature;

use App\Documentation\Repository;
use App\Documentation\Urls;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class DocumentationTest extends TestCase
{
    public static function articles(): array
    {
        $rows = json_decode(file_get_contents(__DIR__.'/../fixtures/documents.json'), true, flags: JSON_THROW_ON_ERROR);

        return array_column(array_map(fn ($row) => ['data' => [$row], 'id' => $row['id']], $rows), 'data', 'id');
    }

    #[DataProvider('articles')]
    public function test_article_has_original_title_text_and_example(array $article): void
    {
        $response = $this->get('/docs/'.$article['id'].'/')->assertOk();
        $response->assertSee('<h1>'.$article['title'].'</h1>', false);
        $response->assertSee($article['description']);
        if (isset($article['example'])) {
            $response->assertSee($article['example']);
        }
        $response->assertDontSee('<footer', false)->assertDontSee('AdminKit PRO');
    }

    public function test_index_has_exactly_the_original_15_plus_6_structure(): void
    {
        $repository = app(Repository::class);
        $navigation = $repository->navigation();
        $this->assertCount(15, $navigation);
        $this->assertCount(6, $navigation[14]['children']);
        $this->assertCount(21, $repository->ordered());
        $this->get('/')->assertOk()->assertSee('Documentación de Binding Identity')
            ->assertDontSee('Funcionalidades')->assertDontSee('<footer', false);
    }

    public function test_all_content_and_corporate_resource_bytes_are_preserved(): void
    {
        $hashes = json_decode(file_get_contents(base_path('tests/fixtures/integrity.json')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertCount(29, $hashes);
        foreach ($hashes as $path => $hash) {
            $this->assertSame($hash, hash_file('sha256', base_path($path)), $path);
        }
    }

    public function test_routes_do_not_touch_the_database_or_start_a_session(): void
    {
        Event::listen(QueryExecuted::class, fn () => $this->fail('El portal no debe ejecutar SQL.'));
        foreach (['/', '/docs/id-validate/', '/docs/authority-reporting/pre-check-in/'] as $path) {
            $this->get($path)->assertOk()->assertHeaderMissing('Set-Cookie');
        }
        $this->assertFileDoesNotExist(base_path('database/database.sqlite'));
        $this->assertSame('array', config('cache.default'));
    }

    public static function unknownPaths(): array
    {
        return [['/pagina-inexistente/'], ['/docs/no-existe/'], ['/docs/authority-reporting/desconocido/'], ['/docs/id-validate/extra/'], ['/docs/../../.env'], ['/404.html'], ['/login'], ['/admin']];
    }

    #[DataProvider('unknownPaths')]
    public function test_unknown_or_traversal_paths_return_the_documentation_404(string $path): void
    {
        $this->get($path)->assertNotFound()->assertSee('Página no encontrada')
            ->assertSee('Volver al índice')->assertDontSee('APP_KEY=');
    }

    public function test_portal_has_only_read_only_routes(): void
    {
        $this->post('/docs/id-validate/')->assertStatus(405);
    }

    public function test_svg_registry_is_optional_and_local(): void
    {
        $this->assertSame([], config('documentation.icons'));
        $this->get('/')->assertDontSee('class="documentation-icon"', false);
        $this->assertSame('/assets/icons/approved.svg', app(Urls::class)->asset('assets/icons/approved.svg'));
    }
}
