<?php

declare(strict_types=1);

namespace Seo\Head\Laravel\Head\Plugins;

use Dom\HTMLDocument;
use Livewire\Mechanisms\FrontendAssets\FrontendAssets;
use Seo\Head\Context\TagsResolvingContext;
use Seo\Head\Head;
use Seo\Head\HeadPlugin;
use Seo\Head\HeadTag;
use Seo\Head\HeadTagPosition;

final readonly class LivewireAssetsPlugin implements HeadPlugin
{
    private function __construct(private bool $useScriptConfig) {}

    public static function make(bool $useScriptConfig = false): self
    {
        return new self($useScriptConfig);
    }

    public function initialize(Head $head): void
    {
        $head->onTagsResolving(function (TagsResolvingContext $context): void {
            /** @var string */
            $styles = FrontendAssets::styles();

            $tag = $this->parseStyles($styles);

            $context->tags->add($tag);

            if ($this->useScriptConfig) {
                /** @var string */
                $scriptConfig = FrontendAssets::scriptConfig();

                $tag = $this->parseScriptConfig($scriptConfig);

                $context->tags->add($tag);
            } else {
                /** @var string */
                $scripts = FrontendAssets::scripts();

                $tag = $this->parseScripts($scripts);

                $context->tags->add($tag);
            }
        });
    }

    private function parseStyles(string $styles): HeadTag
    {
        $dom = HTMLDocument::createFromString($styles, LIBXML_NOERROR);

        $node = $dom->querySelector('style');

        if ($node === null) {
            return new HeadTag(
                type: 'style',
                attributes: [],
                textContent: null,
                position: HeadTagPosition::Head,
            );
        }

        $attributes = [];
        foreach ($node->attributes as $attribute) {
            $attributes[$attribute->name] = $attribute->value;
        }

        return new HeadTag(
            type: 'style',
            attributes: $attributes,
            textContent: $node->textContent,
            position: HeadTagPosition::Head,
        );
    }

    private function parseScriptConfig(string $scriptConfig): HeadTag
    {
        $dom = HTMLDocument::createFromString($scriptConfig, LIBXML_NOERROR);

        $node = $dom->querySelector('script');

        if ($node === null) {
            return new HeadTag(
                type: 'script',
                attributes: [],
                textContent: null,
                position: HeadTagPosition::BodyClose,
            );
        }

        $attributes = [];
        foreach ($node->attributes as $attribute) {
            $attributes[$attribute->name] = $attribute->value;
        }

        return new HeadTag(
            type: 'script',
            attributes: $attributes,
            textContent: $node->textContent,
            position: HeadTagPosition::BodyClose,
        );
    }

    private function parseScripts(string $scripts): HeadTag
    {
        $dom = HTMLDocument::createFromString($scripts, LIBXML_NOERROR);

        $node = $dom->querySelector('script');

        if ($node === null) {
            return new HeadTag(
                type: 'script',
                attributes: [],
                textContent: null,
                position: HeadTagPosition::BodyClose,
            );
        }

        $attributes = [];
        foreach ($node->attributes as $attribute) {
            $attributes[$attribute->name] = $attribute->value;
        }

        return new HeadTag(
            type: 'script',
            attributes: $attributes,
            textContent: $node->textContent,
            position: HeadTagPosition::BodyClose,
        );
    }
}
