<?php

declare(strict_types=1);

namespace App\Modules\Institution\Infrastructure\Identity;

use App\Modules\Institution\Domain\Contracts\InstitutionCodeSequenceInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final readonly class InstitutionCodeSequence implements InstitutionCodeSequenceInterface
{
    private const TABLE = 'institution_code_sequences';

    private const ROW_ID = 1;

    public function next(): int
    {
        $updated = DB::table(self::TABLE)
            ->where('id', self::ROW_ID)
            ->increment('current_value');

        if ($updated !== 1) {
            throw new RuntimeException(
                'Institution code sequence could not be incremented.'
            );
        }

        $value = DB::table(self::TABLE)
            ->where('id', self::ROW_ID)
            ->value('current_value');

        if (! is_int($value) && ! is_numeric($value)) {
            throw new RuntimeException(
                'Institution code sequence returned an invalid value.'
            );
        }

        $value = (int) $value;

        if ($value <= 0) {
            throw new RuntimeException(
                'Institution code sequence must be greater than zero.'
            );
        }

        return $value;
    }
}
