<?php

namespace App\Core;

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

    public static function company(): string
    {
        return config('cn.framework.company');
    }
}
