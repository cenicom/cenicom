<?php

declare(strict_types=1);

namespace App\Core\Actions\Currency;

use App\Core\Actions\BaseAction;
use App\Core\Contracts\ServiceInterface;

class DeleteCurrencyAction extends BaseAction
{
    public function __construct(
        protected ServiceInterface $service
    ) {
    }

    public function execute(
        int|string $id
    ): bool {
        return $this->transaction(
            fn () => $this->service->delete($id)
        );
    }
}
