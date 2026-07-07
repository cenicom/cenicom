<?php

namespace App\Support\Navigation;

class NavigationManager
{
    public function items(): array
    {
        return config('navigation', []);
    }

    public function grouped(): array
    {
        $items = $this->items();

        $menu = [];

        foreach ($items as $item) {

            // Headers (secciones)
            if (isset($item['header'])) {
                $menu[] = $item;
                continue;
            }

            // Items normales o con hijos
            $menu[] = $item;
        }

        return $menu;
    }
}
