<?php

declare(strict_types=1);

namespace App\Modules\City\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Request para actualizar un city.
 *
 * Gestiona la autorización y validación de la actualización
 * de registros del módulo.
 *
 * @package App\Modules\City\Http\Requests
 */
final class UpdateCityRequest
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
                'name' => ['required', 'string'],
            'state_id' => ['required', 'uuid', 'exists:,'],
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
