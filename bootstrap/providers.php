<?php

use App\Providers\AppServiceProvider;
//use App\Providers\CNFrameworkServiceProvider;
use App\Providers\RepositoryServiceProvider;

return [

    AppServiceProvider::class,

    RepositoryServiceProvider::class,
    App\Providers\GeneratorServiceProvider::class,

];
