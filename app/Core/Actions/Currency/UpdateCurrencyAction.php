<?php

declare(strict_types=1);

namespace App\Core\Actions\Currency;

use App\Core\Actions\BaseAction;
use App\Core\Contracts\ServiceInterface;

class UpdateCurrencyAction extends BaseAction
{
    public function __construct(
        protected ServiceInterface $service
    ) {
    }

    public function execute(
        int|string $id,
        array $data
    ): bool {
        return $this->transaction(
            fn () => $this->service->update(
                $id,
                $data
            )
        );
    }
}
