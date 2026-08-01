<?php

declare(strict_types=1);

namespace App\Core\Navigation\Enums;

enum NavigationNodeType: string
{
    case GROUP = 'group';
    case ITEM  = 'item';
}
