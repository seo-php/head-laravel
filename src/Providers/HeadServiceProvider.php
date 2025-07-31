<?php

declare(strict_types=1);

namespace Seo\Head\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Seo\Head\Head;

abstract class HeadServiceProvider extends ServiceProvider
{
    final public function register(): void
    {
        $this->callAfterResolving(Head::class, fn (Head $head) => $this->configure($head));
    }

    abstract protected function configure(Head $head): void;
}
