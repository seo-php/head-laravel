<?php

declare(strict_types=1);

use Illuminate\Foundation\Vite;
use Seo\Head\Head;
use Seo\Head\Laravel\Head\Plugins\LaravelVitePlugin;

it('adds vite link and script tags to the head', function (): void {
    configureHead(function (Head $head): void {
        $head->plugin(
            LaravelVitePlugin::make(
                entrypoints: ['resources/css/app.css', 'resources/js/app.js'],
                buildDirectory: 'build',
            ),
        );
    });

    $vite = Mockery::mock(Vite::class);
    $vite->shouldReceive('__invoke')
        ->once()
        ->with(['resources/css/app.css', 'resources/js/app.js'], 'build')
        ->andReturn(<<<'HTML'
<link rel="stylesheet" href="https://example.com/build/assets/app.css" />
<script type="module" src="https://example.com/build/assets/app.js"></script>
HTML)
    ;

    app()->instance(Vite::class, $vite);

    expect(renderHead(app(Head::class)))->toMatchSnapshot();
});

it('uses default entrypoints when none are provided', function (): void {
    configureHead(function (Head $head): void {
        $head->plugin(LaravelVitePlugin::make());
    });

    $vite = Mockery::mock(Vite::class);
    $vite->shouldReceive('__invoke')
        ->once()
        ->with([], null)
        ->andReturn('<link rel="stylesheet" href="https://example.com/build/assets/app.css" />')
    ;

    app()->instance(Vite::class, $vite);

    expect(renderHead(app(Head::class)))->toMatchSnapshot();
});
