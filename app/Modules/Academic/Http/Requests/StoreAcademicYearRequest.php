<?php

declare(strict_types=1);

namespace App\Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institutionId' => [
                'required',
                'string',
                'exists:institutions,id',
            ],
            'publicationVersionId' => [
                'required',
                'string',
                'exists:official_calendar_publication_versions,id',
            ],
            'cycleCode' => [
                'required',
                'string',
                'max:20',
            ],
            'calendarCode' => [
                'required',
                'in:A,B',
            ],
        ];
    }
}
