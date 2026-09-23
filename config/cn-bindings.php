<?php

declare(strict_types=1);

return [

    App\Modules\City\Domain\Contracts\CityRepositoryInterface::class => App\Modules\City\Repositories\CityRepository::class,
    App\Modules\City\Domain\Contracts\CityServiceInterface::class => App\Modules\City\Domain\Services\CityService::class,
    App\Modules\Country\Domain\Contracts\CountryRepositoryInterface::class => App\Modules\Country\Repositories\CountryRepository::class,
    App\Modules\Country\Domain\Contracts\CountryServiceInterface::class => App\Modules\Country\Domain\Services\CountryService::class,
    App\Modules\Currency\Domain\Contracts\CurrencyRepositoryInterface::class => App\Modules\Currency\Repositories\CurrencyRepository::class,
    App\Modules\Currency\Domain\Contracts\CurrencyServiceInterface::class => App\Modules\Currency\Domain\Services\CurrencyService::class,
    App\Modules\State\Domain\Contracts\StateRepositoryInterface::class => App\Modules\State\Repositories\StateRepository::class,
    App\Modules\State\Domain\Contracts\StateServiceInterface::class => App\Modules\State\Domain\Services\StateService::class,

    App\Modules\CnGeneratorProbe\Domain\Contracts\CnGeneratorProbeRepositoryInterface::class => App\Modules\CnGeneratorProbe\Repositories\CnGeneratorProbeRepository::class,
    App\Modules\CnGeneratorProbe\Domain\Contracts\CnGeneratorProbeServiceInterface::class => App\Modules\CnGeneratorProbe\Domain\Services\CnGeneratorProbeService::class,

];
