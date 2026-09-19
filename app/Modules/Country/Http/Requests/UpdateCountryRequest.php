<?php

declare(strict_types=1);

namespace App\Modules\Country\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Request para actualizar un country.
 *
 * Gestiona la autorización y validación de la actualización
 * de registros del módulo.
 *
 * @package App\Modules\Country\Http\Requests
 */
final class UpdateCountryRequest
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
            'iso2' => ['required', 'string', 'max:2', 'unique:countries,iso2'],
            'iso3' => ['required', 'string', 'max:3', 'unique:countries,iso3'],
        ];
    }

    /**
     * Mensajes personalizados.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Nombres amigables de los atributos.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [];
    }
}
