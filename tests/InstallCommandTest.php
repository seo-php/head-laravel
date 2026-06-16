<?php

declare(strict_types=1);

use function Pest\Laravel\artisan;

it('scaffolds the head service provider', function (): void {
    $providerPath = app_path('Providers/HeadServiceProvider.php');

    if (file_exists($providerPath)) {
        unlink($providerPath);
    }

    artisan('head:install')
        ->assertSuccessful()
    ;

    expect(file_exists($providerPath))->toBeTrue()
        ->and(file_get_contents($providerPath))->toContain('Seo\\Head\\Laravel\\Providers\\HeadServiceProvider')
        ->and(file_get_contents($providerPath))->toContain('LaravelVitePlugin::make')
        ->and(file_get_contents($providerPath))->toContain('resources/css/app.css')
    ;
});

it('refuses to overwrite an existing provider without force', function (): void {
    $providerPath = app_path('Providers/HeadServiceProvider.php');
    $directory = dirname($providerPath);

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    file_put_contents($providerPath, '<?php // existing');

    artisan('head:install')
        ->assertFailed()
    ;

    expect(file_get_contents($providerPath))->toBe('<?php // existing');
});

it('overwrites an existing provider with force', function (): void {
    $providerPath = app_path('Providers/HeadServiceProvider.php');
    $directory = dirname($providerPath);

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    file_put_contents($providerPath, '<?php // existing');

    artisan('head:install', ['--force' => true])
        ->assertSuccessful()
    ;

    expect(file_get_contents($providerPath))->toContain('Seo\\Head\\Laravel\\Providers\\HeadServiceProvider');
});

it('includes the livewire plugin when livewire is installed', function (): void {
    $providerPath = app_path('Providers/HeadServiceProvider.php');

    if (file_exists($providerPath)) {
        unlink($providerPath);
    }

    $composer = Mockery::mock(app('composer'));
    $composer->shouldReceive('hasPackage')->with('livewire/livewire')->andReturn(true);
    app()->instance('composer', $composer);

    artisan('head:install')
        ->assertSuccessful()
    ;

    expect(file_get_contents($providerPath))->toContain('LivewireAssetsPlugin::make');
});
