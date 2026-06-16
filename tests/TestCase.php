<?php

declare(strict_types=1);

namespace Seo\Head\Laravel\Tests;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Seo\Head\Head;
use Seo\Head\Laravel\HeadServiceProvider;
use Seo\Head\Laravel\Providers\HeadServiceProvider as AppHeadServiceProvider;

/**
 * @method \Illuminate\Testing\PendingCommand artisan(string $command, array<string, mixed> $parameters = [])
 */
abstract class TestCase extends OrchestraTestCase
{
    public static ?self $current = null;

    /**
     * @var ?Closure(Head): void
     */
    public static ?Closure $configureUsing = null;

    /**
     * @param Closure(Head): void $configure
     */
    final public static function configureHead(Closure $configure): void
    {
        self::$configureUsing = $configure;
    }

    /**
     * @param Closure(Head): void $configure
     */
    final public function configureHeadForTest(Closure $configure): void
    {
        self::$configureUsing = $configure;
        $this->refreshApplication();
    }

    protected function setUp(): void
    {
        self::$current = $this;

        parent::setUp();
    }

    protected function tearDown(): void
    {
        self::$configureUsing = null;
        self::$current = null;

        parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [
            HeadServiceProvider::class,
            TestAppHeadServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        /** @var Repository $config */
        $config = $app->make('config');
        $config->set('app.url', 'https://example.com');
    }
}

final class TestAppHeadServiceProvider extends AppHeadServiceProvider
{
    protected function configure(Head $head): void
    {
        if (TestCase::$configureUsing !== null) {
            (TestCase::$configureUsing)($head);
        }
    }
}
