<?php

declare(strict_types=1);

namespace App\Core\Generator\Validation\Contracts;


use App\Core\Generator\Specifications\ModuleSpecification;
use App\Core\Generator\Validation\Results\ValidationResult;


/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato base para todos los validadores del CN Generator.
 *
 * Cada implementación valida un aspecto específico del
 * manifiesto del módulo (identidad, campos, relaciones,
 * navegación, permisos, etc.) y devuelve un objeto
 * ValidationResult con los errores, advertencias e
 * información obtenida durante la validación.
 *
 * Los validadores nunca generan archivos ni modifican el
 * manifiesto; únicamente verifican su consistencia.
 *
 * @package App\Core\Generator\Validation\Contracts
 * @since 1.0.0
 */
interface ValidatorInterface
{
    /**
     * Ejecuta la validación correspondiente.
     */
    public function validate(ModuleSpecification $specification): ValidationResult;
}
