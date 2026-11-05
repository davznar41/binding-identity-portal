<?php

namespace Tests\Unit;

use App\Documentation\Urls;
use Illuminate\Http\Request;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UrlsTest extends TestCase
{
    public static function paths(): array
    {
        return [['/', '/', '/'], ['', '/docs/id-validate/', '/docs/id-validate/'], ['/documentacion/', '/', '/documentacion/'], ['/documentacion', '/docs/id-validate/', '/documentacion/docs/id-validate/']];
    }

    #[DataProvider('paths')]
    public function test_prefix_is_not_lost_or_duplicated(string $base, string $path, string $expected): void
    {
        $this->assertSame($expected, Urls::join($base, $path));
    }

    public function test_user_input_cannot_become_a_filesystem_or_external_url(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Urls(Request::create('/')))->article('../../.env');
    }
}
