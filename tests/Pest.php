<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use Seo\Head\Head;
use Seo\Head\Laravel\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * @param Closure(Head): void $configure
 */
function configureHead(Closure $configure): void
{
    if (TestCase::$current instanceof TestCase) {
        TestCase::$current->configureHeadForTest($configure);

        return;
    }

    TestCase::configureHead($configure);
}

function renderHead(Head $head, bool $omitLineBreaks = false): string
{
    $tags = $head->render($omitLineBreaks);

    return <<<HTML
<!DOCTYPE html>
<html {$tags['htmlAttrs']}>
<head>
{$tags['headTags']}
</head>
<body {$tags['bodyAttrs']}>
{$tags['bodyOpenTags']}

{$tags['bodyCloseTags']}
</body>
</html>
HTML;
}

function renderLayout(string $slot = ''): string
{
    return Blade::render('<x-head::layout>'.$slot.'</x-head::layout>');
}

function normalizeLivewireHtml(string $html): string
{
    return preg_replace(
        [
            '/data-csrf="[^"]*"/',
            '/src="[^"]*livewire[^"]*"/',
            '/data-module-url="[^"]*"/',
            '/data-update-uri="[^"]*"/',
            '/--livewire-progress-bar-color: [^;]+;/',
        ],
        [
            'data-csrf="[csrf]"',
            'src="[livewire]"',
            'data-module-url="[module-url]"',
            'data-update-uri="[update-uri]"',
            '--livewire-progress-bar-color: [color];',
        ],
        $html,
    ) ?? $html;
}
