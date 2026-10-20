<?php

declare(strict_types=1);

namespace App\Modules\Institution\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'officialRegistration' => [
                'nullable',
                'array',
            ],

            'officialRegistration.country' => [
                'nullable',
                'string',
                'max:2',
                'required_with:officialRegistration.authority,officialRegistration.value',
            ],

            'officialRegistration.authority' => [
                'nullable',
                'string',
                'max:150',
                'required_with:officialRegistration.country,officialRegistration.value',
            ],

            'officialRegistration.value' => [
                'nullable',
                'string',
                'max:100',
                'required_with:officialRegistration.country,officialRegistration.authority',
            ],
        ];
    }
}
