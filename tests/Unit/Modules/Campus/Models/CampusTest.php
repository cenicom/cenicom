<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Models;

use App\Modules\Campus\Models\Campus;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CampusTest extends TestCase
{
    #[Test]
    public function campus_model_extends_eloquent_model(): void
    {
        $model = new Campus();

        $this->assertInstanceOf(Model::class, $model);
    }

    #[Test]
    public function campus_model_uses_expected_table(): void
    {
        $model = new Campus();

        $this->assertSame(
            'campuses',
            $model->getTable()
        );
    }

    #[Test]
    public function campus_model_uses_string_primary_key(): void
    {
        $model = new Campus();

        $this->assertSame(
            'string',
            $model->getKeyType()
        );

        $this->assertFalse(
            $model->getIncrementing()
        );
    }

    #[Test]
    public function campus_model_uses_ulids(): void
    {
        $model = new Campus();

        $this->assertSame(
            26,
            strlen($model->newUniqueId())
        );
    }

    #[Test]
    public function campus_model_defines_expected_fillable_attributes(): void
    {
        $model = new Campus();

        $this->assertSame(
            [
                'id',
                'institution_id',
                'code',
                'short_code',
                'name',
                'ministry_code',
                'address_id',
                'status',
            ],
            $model->getFillable()
        );
    }
}
