<?php

declare(strict_types=1);

namespace Seo\Head\Laravel\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Seo\Head\Head;

final class Layout extends Component
{
    public function __construct(private readonly Head $head) {}

    public function render(): View
    {
        return $this->view('head::components.layout', $this->head->render());
    }
}
