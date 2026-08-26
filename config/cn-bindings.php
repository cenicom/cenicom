<?php

declare(strict_types=1);

return [

    App\Modules\GeneratorProbe\Domain\Contracts\GeneratorProbeRepositoryInterface::class => App\Modules\GeneratorProbe\Repositories\GeneratorProbeRepository::class,
    App\Modules\GeneratorProbe\Domain\Contracts\GeneratorProbeServiceInterface::class => App\Modules\GeneratorProbe\Services\GeneratorProbeService::class,

];
