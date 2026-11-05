<?php

namespace App\Providers;

use App\Documentation\Repository;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Repository::class, fn () => new Repository(
            config('documentation.content_path'), config('documentation.primary'), config('documentation.children'),
        ));
    }
}
