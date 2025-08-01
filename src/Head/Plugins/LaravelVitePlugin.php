<?php

declare(strict_types=1);

namespace Seo\Head\Laravel\Head\Plugins;

use Illuminate\Foundation\Vite;
use Seo\Head\Head;
use Seo\Head\HeadHook;
use Seo\Head\HeadPlugin;
use Seo\Head\Hooks\RenderedHookContext;

final readonly class LaravelVitePlugin implements HeadPlugin
{
    /**
     * @param array<string> $entrypoints
     */
    private function __construct(private array $entrypoints, private ?string $buildDirectory) {}

    /**
     * @param array<string> $entrypoints
     */
    public static function make(array $entrypoints = [], ?string $buildDirectory = null): HeadPlugin
    {
        return new self($entrypoints, $buildDirectory);
    }

    public function initialize(Head $head): void
    {
        $head->hook(
            HeadHook::Rendered->value,
            function (RenderedHookContext $context): void {
                $contents = app(Vite::class)->__invoke($this->entrypoints, $this->buildDirectory);

                $context->tags['head'] .= $contents->toHtml();
            },
        );
    }
}
