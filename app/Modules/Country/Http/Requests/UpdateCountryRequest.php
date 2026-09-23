<?php

declare(strict_types=1);

namespace App\Modules\Country\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $country = $this->route('country');

        return [
            'name' => ['required', 'string'],

            'iso2' => [
                'required',
                'string',
                'max:2',
                Rule::unique('countries', 'iso2')
                    ->ignore($country),
            ],

            'iso3' => [
                'required',
                'string',
                'max:3',
                Rule::unique('countries', 'iso3')
                    ->ignore($country),
            ],
        ];
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [];
    }
}
