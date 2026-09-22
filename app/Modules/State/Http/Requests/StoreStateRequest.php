<?php

declare(strict_types=1);

namespace App\Modules\State\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Request para crear un state.
 *
 * Gestiona la autorización y validación de la creación
 * de registros del módulo.
 *
 * @package App\Modules\State\Http\Requests
 */
final class StoreStateRequest
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
            'country_id' => ['required', 'uuid', 'exists:countries,id'],
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
