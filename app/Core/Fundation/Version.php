<?php

namespace App\Core\Foundation;

final class Version
{
    public static function framework(): string
    {
        return config('cn.framework.version');
    }

    public static function build(): string
    {
        return config('cn.framework.build');
    }

    public static function name(): string
    {
        return config('cn.framework.name');
    }
}
