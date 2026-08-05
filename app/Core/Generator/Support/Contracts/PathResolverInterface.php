<?php

namespace App\Core\Generator\Support\Contracts;

/*  */
interface PathResolverInterface
{
    public function base(string $path = ''): string;

    public function app(string $path = ''): string;

    public function resource(string $path = ''): string;

    public function database(string $path = ''): string;

    public function routes(string $path = ''): string;




}
