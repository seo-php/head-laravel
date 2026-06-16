<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Seo\Head\Head;
use Seo\Head\Laravel\Console\Commands\InstallCommand;

it('registers head as a singleton', function (): void {
    $first = app(Head::class);
    $second = app(Head::class);

    expect($first)->toBeInstanceOf(Head::class)
        ->and($second)->toBe($first)
    ;
});

it('aliases head in the container', function (): void {
    expect(app('head'))->toBe(app(Head::class));
});

it('loads the layout view', function (): void {
    expect(view()->exists('head::components.layout'))->toBeTrue();
});

it('registers the layout blade component', function (): void {
    expect(renderLayout('Hello'))->toContain('Hello');
});

it('registers the install command', function (): void {
    expect(array_key_exists('head:install', Artisan::all()))->toBeTrue()
        ->and(Artisan::all()['head:install'])->toBeInstanceOf(InstallCommand::class)
    ;
});

it('configures head through the application provider', function (): void {
    $configured = false;

    configureHead(function (Head $head) use (&$configured): void {
        $configured = true;
        $head->title('Configured Title');
    });

    $head = app(Head::class);

    expect($configured)->toBeTrue()
        ->and($head->render()['headTags'])->toContain('<title>Configured Title</title>')
    ;
});
