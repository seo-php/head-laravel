<?php

declare(strict_types=1);

namespace Seo\Head\Laravel\Head\Plugins;

use Dom\HTMLDocument;
use Illuminate\Foundation\Vite;
use Seo\Head\Context\TagsResolvingContext;
use Seo\Head\Head;
use Seo\Head\HeadPlugin;
use Seo\Head\HeadTag;
use Seo\Head\HeadTagPosition;

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
        $head->onTagsResolving(function (TagsResolvingContext $context): void {
            $contents = app(Vite::class)->__invoke($this->entrypoints, $this->buildDirectory);

            $tags = $this->parseContents((string) $contents);

            foreach ($tags as $tag) {
                $context->tags->add($tag);
            }
        });
    }

    /**
     * @return array<HeadTag>
     */
    private function parseContents(string $contents): array
    {
        $dom = HTMLDocument::createFromString($contents, LIBXML_NOERROR);

        $nodes = $dom->querySelectorAll('link, script');

        $tags = [];
        foreach ($nodes as $node) {
            $attributes = [];
            foreach ($node->attributes as $attribute) {
                $attributes[$attribute->name] = $attribute->value;
            }

            $tags[] = new HeadTag(
                type: $node->localName,
                attributes: $attributes,
                textContent: $node->textContent,
                position: HeadTagPosition::Head,
            );
        }

        return $tags;
    }
}
