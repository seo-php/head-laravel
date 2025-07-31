<?php

declare(strict_types=1);

namespace Seo\Head\Laravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Seo\Head\Laravel\Head\Plugins\LaravelVitePlugin;
use Seo\Head\Laravel\Head\Plugins\LivewireAssetsPlugin;

final class InstallCommand extends Command
{
    protected $signature = 'head:install {--F|force}';

    protected $description = 'Install the Head package';

    public function __invoke(): int
    {
        $this->comment('Installing Head package...');

        $appNamespace = trim($this->laravel->getNamespace(), '\\');

        $providerPath = app_path('Providers/HeadServiceProvider.php');

        if (file_exists($providerPath) && !$this->option('force')) {
            $this->error("{$providerPath} already exists. Use the --force option to overwrite.");
            return self::FAILURE;
        }

        $stub = file_get_contents(__DIR__.'/../../../stubs/HeadServiceProvider.stub');
        if ($stub === false) {
            $this->error('Failed to read the HeadServiceProvider.stub file.');
            return self::FAILURE;
        }

        $stub = str_replace('DummyNamespace', "{$appNamespace}\\Providers", $stub);

        $pluginUses = [
            'use '.LaravelVitePlugin::class.';',
        ];

        $plugins = [
            'LaravelVitePlugin::make(entrypoints: [\'resources/css/app.css\', \'resources/js/app.js\']),',
        ];

        /** @var \Illuminate\Support\Composer */
        $composer = $this->laravel->make('composer');

        if ($composer->hasPackage('livewire/livewire')) {
            $pluginUses[] = 'use '.LivewireAssetsPlugin::class.';';
            $plugins[] = 'LivewireAssetsPlugin::make(useScriptConfig: false),';
        }

        $stub = str_replace('DummyPluginUses', implode("\n", $pluginUses), $stub);
        $stub = str_replace('DummyPlugins', implode("\n            ", $plugins), $stub);

        file_put_contents($providerPath, $stub);

        ServiceProvider::addProviderToBootstrapFile("{$appNamespace}\\Providers\\HeadServiceProvider");

        $this->info('Head package installed successfully.');

        return self::SUCCESS;
    }
}
