<?php

declare(strict_types=1);

namespace App\Core\Navigation\Services;

use App\Core\Navigation\Contracts\NavigationBuilderInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Navigation\DTO\NavigationTreeData;

final readonly class NavigationService implements NavigationServiceInterface
{
    public function __construct(
        private NavigationRegistryInterface $registry,
        private NavigationBuilderInterface $builder,
    ) {
    }

    public function tree(): NavigationTreeData
    {
        $tree = $this->registry->tree();

        if (! $tree->isEmpty()) {
            return $tree;
        }

        $tree = $this->builder->build();

        $this->registry->setTree($tree);

        return $tree;
    }
}
