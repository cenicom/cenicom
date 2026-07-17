<?php


use App\Providers\AppServiceProvider;
use App\Providers\CNFrameworkServiceProvider;
use App\Providers\CNGeneratorServiceProvider;
//use App\Providers\GeneratorServiceProvider;
use App\Providers\RepositoryServiceProvider;

return [

    AppServiceProvider::class,


    RepositoryServiceProvider::class,

    CNGeneratorServiceProvider::class,

    CNFrameworkServiceProvider::class,

    //GeneratorServiceProvider::class,

];
