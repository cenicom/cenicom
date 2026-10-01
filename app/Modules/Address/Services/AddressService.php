<?php

declare(strict_types=1);

namespace App\Modules\Address\Services;

use App\Core\Services\BaseService;
use App\Modules\Address\Domain\Contracts\AddressRepositoryInterface;
use App\Modules\Address\Domain\Contracts\AddressServiceInterface;

final class AddressService extends BaseService implements AddressServiceInterface
{
    public function __construct(
        AddressRepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
