<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Models;

use App\Modules\Institution\Models\Institution;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class InstitutionTest extends TestCase
{
    #[Test]
    public function institution_model_extends_eloquent_model(): void
    {
        $model = new Institution();

        $this->assertInstanceOf(Model::class, $model);
    }

    #[Test]
    public function institution_model_uses_expected_table(): void
    {
        $model = new Institution();

        $this->assertSame(
            'institutions',
            $model->getTable()
        );
    }

    #[Test]
    public function institution_model_uses_string_primary_key(): void
    {
        $model = new Institution();

        $this->assertSame(
            'string',
            $model->getKeyType()
        );

        $this->assertFalse(
            $model->getIncrementing()
        );
    }

    #[Test]
    public function institution_model_uses_ulids(): void
    {
        $model = new Institution();

        $this->assertSame(
            26,
            strlen($model->newUniqueId())
        );
    }

    #[Test]
    public function institution_model_defines_expected_fillable_attributes(): void
    {
        $model = new Institution();

        $this->assertSame(
            [
                'id',
                'code',
                'name',
                'official_registration_country',
                'official_registration_authority',
                'official_registration_value',
                'status',
            ],
            $model->getFillable()
        );
    }
}
