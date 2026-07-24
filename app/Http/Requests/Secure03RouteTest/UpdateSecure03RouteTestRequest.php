<?php

declare(strict_types=1);

namespace App\Http\Requests\Secure03RouteTest;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Request para actualizar un secure03_route_test.
 *
 * Gestiona la autorización y validación de la actualización
 * de registros del módulo.
 *
 * @package App\Http\Requests\Secure03RouteTest
 */
final class UpdateSecure03RouteTestRequest
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
        return [

        ];
    }

    /**
     * Nombres amigables de los atributos.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [

        ];
    }
}
