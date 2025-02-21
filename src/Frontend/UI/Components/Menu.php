<?php

namespace Swark\Frontend\UI\Components;

use Illuminate\View\Component;

/**
 * @see https://sandbox.elemisthemes.com/docs/blocks/navbar.html
 */
class Menu extends Component
{
    public function __construct(public readonly mixed $items)
    {
    }

    public function attrToggle(array $item): string {
        if (sizeof($item['children']) > 0) {
            return 'data-bs-toggle="dropdown"';
        }

        return '';
    }
    public function aClasses(array $item): string
    {
        $classes = [];

        if ($item['depth'] == 0) {
            $classes[] = 'nav-link p-2';
        }
        else {
            $classes[] = 'dropdown-item';
        }

        if (sizeof($item['children']) > 0) {
            $classes[] = 'dropdown-toggle';
        }

        if ($item['active']) {
            $classes[] = 'active';
        }

        return implode(" ", $classes);
    }

    public function liCssClasses(array $item): string
    {
        $classes = [];

        if (($item['depth'] == 0) || (sizeof($item['children']) > 0 && $item['depth'] == 1)) {
            $classes[] = 'nav-item col-6 col-md-auto';
        }

        if (sizeof($item['children']) > 0) {
            $classes[] = 'dropdown';

            if ($item['depth'] >= 1) {
                $classes[] = 'dropdown-submenu dropend';
            }
        }

        if ($item['active']) {
            $classes[] = 'active';
        }

        return implode(" ", $classes);
    }

    public function clazz()
    {
        return "sss";
    }

    public function render()
    {
        return view('swark::components.menu', [
            'items' => $this->items
        ]);
    }
}
