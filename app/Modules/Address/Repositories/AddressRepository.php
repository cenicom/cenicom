<?php

declare(strict_types=1);

namespace App\Modules\Address\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Modules\Address\Domain\Contracts\AddressRepositoryInterface;
use App\Modules\Address\Models\Address;

final class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    public function __construct(Address $model)
    {
        parent::__construct($model);
    }
}
