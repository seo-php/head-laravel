<?php

declare(strict_types=1);

use Seo\Head\Head;

it('renders the layout component', function (): void {
    expect(renderLayout())->toMatchSnapshot();
});

it('renders the layout component with head tags', function (): void {
    configureHead(function (Head $head): void {
        $head->title('Page Title');
        $head->meta(name: 'description', content: 'A page description');
        $head->html(lang: 'en');
    });

    expect(renderLayout('Hello world'))->toMatchSnapshot();
});

it('renders slot content inside the body', function (): void {
    configureHead(function (Head $head): void {
        $head->title('Slot Test');
    });

    expect(renderLayout('<main>Custom content</main>'))->toContain('<main>Custom content</main>');
});
