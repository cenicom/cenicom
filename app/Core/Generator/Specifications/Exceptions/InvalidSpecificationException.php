<?php

declare(strict_types=1);

namespace App\Core\Generator\Specifications\Exceptions;

use RuntimeException;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Excepción lanzada cuando una especificación
 * de módulo es inválida o no puede cargarse.
 *
 * Utilizada por los loaders y el validador
 * del subsistema Specifications.
 *
 * @package App\Core\Generator\Specifications\Exceptions
 */
final class InvalidSpecificationException extends RuntimeException
{
}
