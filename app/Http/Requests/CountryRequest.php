<?php

namespace App\Http\Requests;

//use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class CountryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'code' => [
                'required',
                'string',
                'size:2',
            ],
            'iso3' => [
                'required',
                'string',
                'size:3',
                'unique:countries,iso3',
            ],
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'nationality' => 'required|string|max:100',
            'phone_code' => 'nullable|string|max:10',
            'currency_code' => 'nullable|string|max:5',
            'language' => 'required|string|max:5',
            'active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }
}
