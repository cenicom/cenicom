<?php

declare(strict_types=1);

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * -----------------------------------------------------------------------------
 * CENICOM ERP
 * -----------------------------------------------------------------------------
 *
 * Módulo      : Currency
 * Componente  : UpdateCurrencyRequest
 * Versión     : 1.0.0
 *
 * Responsabilidad:
 * Validar y normalizar los datos necesarios para actualizar una moneda.
 */
class UpdateCurrencyRequest extends FormRequest
{
    /**
     * Determina si el usuario puede realizar esta petición.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        $currency = $this->route('currency');

        return [
            'code' => [
                'required',
                'string',
                'size:3',
                'alpha',
                Rule::unique('currencies', 'code')->ignore($currency),
            ],

            'symbol' => [
                'required',
                'string',
                'max:10',
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'decimal_places' => [
                'required',
                'integer',
                'between:0,8',
            ],

            'decimal_separator' => [
                'required',
                'string',
                'max:1',
            ],

            'thousands_separator' => [
                'required',
                'string',
                'max:1',
            ],

            'symbol_position' => [
                'required',
                Rule::in(['before', 'after']),
            ],

            'is_default' => [
                'boolean',
            ],

            'status' => [
                'boolean',
            ],
        ];
    }

    /**
     * Preparar datos antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(trim((string) $this->code)),
            'name' => trim((string) $this->name),
            'symbol' => trim((string) $this->symbol),
            'decimal_separator' => trim((string) $this->decimal_separator),
            'thousands_separator' => trim((string) $this->thousands_separator),
            'symbol_position' => strtolower(trim((string) $this->symbol_position)),
            'is_default' => $this->boolean('is_default'),
            'status' => $this->boolean('status'),
        ]);
    }

    /**
     * Nombres amigables.
     */
    public function attributes(): array
    {
        return [
            'code' => 'código',
            'symbol' => 'símbolo',
            'name' => 'nombre',
            'decimal_places' => 'cantidad de decimales',
            'decimal_separator' => 'separador decimal',
            'thousands_separator' => 'separador de miles',
            'symbol_position' => 'posición del símbolo',
            'is_default' => 'moneda predeterminada',
            'status' => 'estado',
        ];
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [
            'code.unique' => 'Ya existe una moneda con ese código.',
            'code.size' => 'El código debe tener exactamente 3 caracteres.',
            'symbol_position.in' => 'La posición del símbolo es inválida.',
        ];
    }
}
