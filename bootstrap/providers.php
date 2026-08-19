<?php

use App\Core\Audit\Providers\AuditServiceProvider;
use App\Core\Module\Providers\ModuleServiceProvider;
use App\Core\Navigation\NavigationServiceProvider;
use App\Core\Security\Providers\SecurityServiceProvider;
use App\Modules\Institution\Providers\InstitutionServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\CNFrameworkServiceProvider;
use App\Providers\CNGeneratorServiceProvider;
use App\Providers\RepositoryServiceProvider;

return [

    AppServiceProvider::class,

    RepositoryServiceProvider::class,

    CNGeneratorServiceProvider::class,

    CNFrameworkServiceProvider::class,

    NavigationServiceProvider::class,

    InstitutionServiceProvider::class,

    ModuleServiceProvider::class,

    SecurityServiceProvider::class,

    AuditServiceProvider::class,


];
