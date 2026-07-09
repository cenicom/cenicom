<?php

declare(strict_types=1);

namespace App\Core\Actions\Currency;

use App\Core\Actions\BaseAction;
use App\Core\Contracts\ServiceInterface;
use Illuminate\Database\Eloquent\Model;

class CreateCurrencyAction extends BaseAction
{
    public function __construct(
        protected ServiceInterface $service
    ) {
    }

    public function execute(
        array $data
    ): Model {
        return $this->transaction(
            fn () => $this->service->create($data)
        );
    }
}
