<?php

declare(strict_types=1);

namespace App\Modules\CnGeneratorProbe\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Request para crear un cn_generator_probe.
 *
 * Gestiona la autorización y validación de la creación
 * de registros del módulo.
 *
 * @package App\Modules\CnGeneratorProbe\Http\Requests
 */
final class StoreCnGeneratorProbeRequest
    extends FormRequest
{
    /**
     * Determina si el usuario está autorizado.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
    return [
    
    ];
    }

    /**
     * Mensajes personalizados.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [ ];
    }

    /**
     * Nombres amigables de los atributos.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [ ];
    }
}
