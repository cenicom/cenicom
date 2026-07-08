<?php

declare(strict_types=1);

namespace App\Core\Data;

use App\Core\Contracts\DataInterface;

abstract class BaseData implements DataInterface
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
