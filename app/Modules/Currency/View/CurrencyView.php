<?php

declare(strict_types=1);

namespace App\Modules\Currency\View;

use App\Core\View\Contracts\ViewDefinitionInterface;
use App\Core\View\Contracts\ViewRegistrarInterface;

final class CurrencyView implements ViewDefinitionInterface
{
    public function register(
        ViewRegistrarInterface $views
    ): void {
        $views->register(
            'currencies',
            'app/Modules/Currency/Resources/Views',
        );
    }
}
