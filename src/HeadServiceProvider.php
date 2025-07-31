<?php

declare(strict_types=1);

namespace Seo\Head\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Seo\Head\Head;
use Seo\Head\Laravel\Console\Commands;

final class HeadServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singletonIf(Head::class, fn () => Head::create());

        $this->app->alias(Head::class, 'head');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'head');

        $this->callAfterResolving(BladeCompiler::class, function (BladeCompiler $blade): void {
            $blade->componentNamespace('Seo\\Head\\Laravel\\View\\Components', 'head');
        });
    }

    private function bootForConsole(): void
    {
        $this->commands([
            Commands\InstallCommand::class,
        ]);
    }
}
