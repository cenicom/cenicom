<?php

declare(strict_types=1);

namespace App\Http\Requests\TestModuleForm;

use Illuminate\Foundation\Http\FormRequest;



/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Request para crear un test_module_form.
 *
 * Gestiona la autorización y validación de la creación
 * de registros del módulo.
 *
 * @package App\Http\Requests\TestModuleForm
 */
final class StoreTestModuleFormRequest
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
