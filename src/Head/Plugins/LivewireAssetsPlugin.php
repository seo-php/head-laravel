<?php

declare(strict_types=1);

namespace Seo\Head\Laravel\Head\Plugins;

use Livewire\Mechanisms\FrontendAssets\FrontendAssets;
use Seo\Head\Context\RenderedContext;
use Seo\Head\Head;
use Seo\Head\HeadPlugin;

final readonly class LivewireAssetsPlugin implements HeadPlugin
{
    private function __construct(private bool $useScriptConfig) {}

    public static function make(bool $useScriptConfig = false): self
    {
        return new self($useScriptConfig);
    }

    public function initialize(Head $head): void
    {
        $head->onRendered(function (RenderedContext $context): void {
            /** @var string */
            $styles = FrontendAssets::styles();
            $context->tags['head'] .= $styles;

            if ($this->useScriptConfig) {
                /** @var string */
                $scriptConfig = FrontendAssets::scriptConfig();
                $context->tags['bodyClose'] .= $scriptConfig;
            } else {
                /** @var string */
                $scripts = FrontendAssets::scripts();
                $context->tags['bodyClose'] .= $scripts;
            }
        });
    }
}
