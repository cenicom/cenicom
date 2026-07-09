<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class ConfigurationException extends CoreException
{
    protected string $errorCode = 'CONFIGURATION_ERROR';

    protected int $httpStatus = 500;
}
