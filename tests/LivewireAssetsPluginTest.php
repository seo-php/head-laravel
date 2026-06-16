<?php

declare(strict_types=1);

use Livewire\Mechanisms\FrontendAssets\FrontendAssets;
use Seo\Head\Head;
use Seo\Head\Laravel\Head\Plugins\LivewireAssetsPlugin;

beforeEach(function (): void {
    app(FrontendAssets::class)->hasRenderedStyles = false;
    app(FrontendAssets::class)->hasRenderedScripts = false;
});

it('adds livewire styles and scripts', function (): void {
    configureHead(function (Head $head): void {
        $head->plugin(LivewireAssetsPlugin::make());
    });

    $rendered = renderHead(app(Head::class));

    expect(normalizeLivewireHtml($rendered))->toMatchSnapshot();
});

it('adds livewire script config when enabled', function (): void {
    configureHead(function (Head $head): void {
        $head->plugin(LivewireAssetsPlugin::make(useScriptConfig: true));
    });

    $rendered = renderHead(app(Head::class));

    expect(normalizeLivewireHtml($rendered))->toMatchSnapshot();
});
