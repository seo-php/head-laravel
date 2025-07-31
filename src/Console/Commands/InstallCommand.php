<?php

declare(strict_types=1);

namespace Seo\Head\Laravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use Seo\Head\Head;
use Seo\Head\Laravel\Head\Plugins\LaravelVitePlugin;
use Seo\Head\Laravel\Head\Plugins\LivewireAssetsPlugin;
use Seo\Head\Laravel\Providers\HeadServiceProvider;

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

        $this->generateProvider($providerPath, $appNamespace, $this->getPlugins());

        ServiceProvider::addProviderToBootstrapFile("{$appNamespace}\\Providers\\HeadServiceProvider");

        $this->info('Head package installed successfully.');

        return self::SUCCESS;
    }

    /**
     * @return array<class-string, string>
     */
    private function getPlugins(): array
    {
        $plugins = [
            LaravelVitePlugin::class => 'LaravelVitePlugin::make(entrypoints: [\'resources/css/app.css\', \'resources/js/app.js\']),',
        ];

        /** @var \Illuminate\Support\Composer */
        $composer = $this->laravel->make('composer');

        if ($composer->hasPackage('livewire/livewire')) {
            $plugins[LivewireAssetsPlugin::class] = 'LivewireAssetsPlugin::make(useScriptConfig: false),';
        }

        return $plugins;
    }

    /**
     * @param array<class-string, string> $plugins
     */
    private function generateProvider(string $providerPath, string $appNamespace, array $plugins): void
    {
        $file = new PhpFile();

        $namespace = $file->addNamespace("{$appNamespace}\\Providers");

        $namespace->addUse(Head::class);
        $namespace->addUse(HeadServiceProvider::class, 'ServiceProvider');

        foreach (array_keys($plugins) as $plugin) {
            $namespace->addUse($plugin);
        }

        $class = $namespace->addClass('HeadServiceProvider');

        $class->setExtends(HeadServiceProvider::class);

        $configure = $class->addMethod('configure');

        $configure
            ->setProtected()
            ->setReturnType('void')
        ;

        $configure->addParameter('head')->setType(Head::class);

        $configure->addBody('$head->plugins([');

        foreach ($plugins as $plugin) {
            $configure->addBody("\t{$plugin}");
        }

        $configure->addBody(']);');

        $configure->addBody('');

        $configure
            ->addBody('$head->html(lang: str_replace(\'_\', \'-\', $this->app->getLocale()));')
            ->addBody('$head->meta(charset: \'utf-8\');')
            ->addBody('$head->meta(name: \'viewport\', content: \'width=device-width, initial-scale=1.0\');')
        ;

        file_put_contents($providerPath, new PsrPrinter()->printFile($file));
    }
}
