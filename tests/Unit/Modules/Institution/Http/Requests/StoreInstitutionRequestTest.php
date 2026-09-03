<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Http\Requests;


use App\Modules\Institution\Http\Requests\StoreInstitutionRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

final class StoreInstitutionRequestTest extends TestCase
{
    public function test_accepts_valid_institution_data(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => 'Institución Educativa Nacional',
            'officialRegistration' => [
                'country' => 'CO',
                'authority' => 'Education Authority',
                'value' => 'REG-001',
            ],
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertFalse($validator->fails());
    }

    public function test_requires_name(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'officialRegistration' => [
                'country' => 'CO',
                'authority' => 'Education Authority',
                'value' => 'REG-001',
            ],
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_name_must_be_string(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => 123,
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_name_has_maximum_length_of_255(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => str_repeat('A', 256),
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_official_registration_is_optional(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => 'Institución Educativa Nacional',
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertFalse($validator->fails());
    }

    public function test_official_registration_must_be_an_array(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => 'Institución Educativa Nacional',
            'officialRegistration' => 'invalid',
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration',
            $validator->errors()->toArray()
        );
    }

    public function test_official_registration_country_is_required_when_registration_is_present(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => 'Institución Educativa Nacional',
            'officialRegistration' => [
                'authority' => 'Education Authority',
                'value' => 'REG-001',
            ],
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.country',
            $validator->errors()->toArray()
        );
    }

    public function test_official_registration_authority_is_required_when_registration_is_present(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => 'Institución Educativa Nacional',
            'officialRegistration' => [
                'country' => 'CO',
                'value' => 'REG-001',
            ],
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.authority',
            $validator->errors()->toArray()
        );
    }

    public function test_official_registration_value_is_required_when_registration_is_present(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => 'Institución Educativa Nacional',
            'officialRegistration' => [
                'country' => 'CO',
                'authority' => 'Education Authority',
            ],
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.value',
            $validator->errors()->toArray()
        );
    }

    public function test_official_registration_country_has_maximum_length_of_2(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => 'Institución Educativa Nacional',
            'officialRegistration' => [
                'country' => 'COL',
                'authority' => 'Education Authority',
                'value' => 'REG-001',
            ],
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.country',
            $validator->errors()->toArray()
        );
    }

    public function test_official_registration_authority_has_maximum_length_of_150(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => 'Institución Educativa Nacional',
            'officialRegistration' => [
                'country' => 'CO',
                'authority' => str_repeat('A', 151),
                'value' => 'REG-001',
            ],
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.authority',
            $validator->errors()->toArray()
        );
    }

    public function test_official_registration_value_has_maximum_length_of_100(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => 'Institución Educativa Nacional',
            'officialRegistration' => [
                'country' => 'CO',
                'authority' => 'Education Authority',
                'value' => str_repeat('A', 101),
            ],
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.value',
            $validator->errors()->toArray()
        );
    }

    public function test_does_not_require_generated_id_code_or_status(): void
    {
        $request = new StoreInstitutionRequest();

        $data = [
            'name' => 'Institución Educativa Nacional',
        ];

        $validator = Validator::make($data, $request->rules());

        self::assertFalse($validator->fails());
    }
}
