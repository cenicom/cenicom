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
        $validator = Validator::make(
            $this->validData(),
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertFalse($validator->fails());
    }

    public function test_requires_name(): void
    {
        $data = $this->validData();
        unset($data['name']);

        $validator = Validator::make(
            $data,
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_name_must_be_string(): void
    {
        $data = $this->validData();
        $data['name'] = 123;

        $validator = Validator::make(
            $data,
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_name_has_maximum_length_of_255(): void
    {
        $data = $this->validData();
        $data['name'] = str_repeat('A', 256);

        $validator = Validator::make(
            $data,
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertTrue($validator->fails());
        self::assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_official_registration_is_required(): void
    {
        $data = $this->validData();
        unset($data['officialRegistration']);

        $validator = Validator::make(
            $data,
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration',
            $validator->errors()->toArray(),
        );
    }

    public function test_official_registration_must_be_an_array(): void
    {
        $data = $this->validData();
        $data['officialRegistration'] = 'invalid';

        $validator = Validator::make(
            $data,
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration',
            $validator->errors()->toArray(),
        );
    }

    public function test_official_registration_country_is_required_when_registration_is_present(): void
    {
        $data = $this->validData();
        unset($data['officialRegistration']['country']);

        $validator = Validator::make(
            $data,
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.country',
            $validator->errors()->toArray(),
        );
    }

    public function test_official_registration_authority_is_required_when_registration_is_present(): void
    {
        $data = $this->validData();
        unset($data['officialRegistration']['authority']);

        $validator = Validator::make(
            $data,
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.authority',
            $validator->errors()->toArray(),
        );
    }

    public function test_official_registration_value_is_required_when_registration_is_present(): void
    {
        $data = $this->validData();
        unset($data['officialRegistration']['value']);

        $validator = Validator::make(
            $data,
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.value',
            $validator->errors()->toArray(),
        );
    }

    public function test_official_registration_country_has_maximum_length_of_2(): void
    {
        $data = $this->validData();
        $data['officialRegistration']['country'] = 'COL';

        $validator = Validator::make(
            $data,
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.country',
            $validator->errors()->toArray(),
        );
    }

    public function test_official_registration_authority_has_maximum_length_of_150(): void
    {
        $data = $this->validData();
        $data['officialRegistration']['authority'] = str_repeat('A', 151);

        $validator = Validator::make(
            $data,
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.authority',
            $validator->errors()->toArray(),
        );
    }

    public function test_official_registration_value_has_maximum_length_of_100(): void
    {
        $data = $this->validData();
        $data['officialRegistration']['value'] = str_repeat('A', 101);

        $validator = Validator::make(
            $data,
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertTrue($validator->fails());
        self::assertArrayHasKey(
            'officialRegistration.value',
            $validator->errors()->toArray(),
        );
    }

    public function test_does_not_require_generated_id_code_or_status(): void
    {
        $validator = Validator::make(
            $this->validData(),
            $this->rulesWithoutDatabaseConstraints(),
        );

        self::assertFalse($validator->fails());
    }

    /**
     * @return array<string, mixed>
     */
    private function validData(): array
    {
        return [
            'name' => 'Institución Educativa Nacional',
            'shortCode' => 'IESBSC',
            'officialRegistration' => [
                'country' => 'CO',
                'authority' => 'Education Authority',
                'value' => 'REG-001',
            ],
        ];
    }

    /**
     * Keep these unit tests focused on the format and required-field rules.
     * Database-backed uniqueness is covered by the HTTP feature test.
     *
     * @return array<string, mixed>
     */
    private function rulesWithoutDatabaseConstraints(): array
    {
        $rules = (new StoreInstitutionRequest())->rules();

        $rules['shortCode'] = array_values(array_filter(
            $rules['shortCode'],
            static fn (mixed $rule): bool => ! (
                is_string($rule) && str_starts_with($rule, 'unique:')
            ),
        ));

        return $rules;
    }
}
