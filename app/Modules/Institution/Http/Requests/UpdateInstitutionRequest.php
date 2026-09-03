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
                'required_with:officialRegistration',
                'string',
                'max:2',
            ],

            'officialRegistration.authority' => [
                'required_with:officialRegistration',
                'string',
                'max:150',
            ],

            'officialRegistration.value' => [
                'required_with:officialRegistration',
                'string',
                'max:100',
            ],
        ];
    }
}
