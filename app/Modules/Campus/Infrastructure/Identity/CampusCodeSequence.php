<?php

declare(strict_types=1);

namespace App\Modules\Campus\Infrastructure\Identity;

use App\Modules\Campus\Domain\Contracts\CampusCodeSequenceInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final readonly class CampusCodeSequence implements CampusCodeSequenceInterface
{
    private const TABLE = 'campus_code_sequences';

    public function next(string $institutionId): int
    {
        if (trim($institutionId) === '') {
            throw new RuntimeException(
                'Campus code sequence requires an institution id.'
            );
        }

        $exists = DB::table(self::TABLE)
            ->where('institution_id', $institutionId)
            ->exists();

        if (! $exists) {
            DB::table(self::TABLE)->insert([
                'institution_id' => $institutionId,
                'current_value' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $updated = DB::table(self::TABLE)
            ->where('institution_id', $institutionId)
            ->increment('current_value');

        if ($updated !== 1) {
            throw new RuntimeException(
                'Campus code sequence could not be incremented.'
            );
        }

        $value = DB::table(self::TABLE)
            ->where('institution_id', $institutionId)
            ->value('current_value');

        if (! is_int($value) && ! is_numeric($value)) {
            throw new RuntimeException(
                'Campus code sequence returned an invalid value.'
            );
        }

        $value = (int) $value;

        if ($value <= 0) {
            throw new RuntimeException(
                'Campus code sequence must be greater than zero.'
            );
        }

        return $value;
    }
}
