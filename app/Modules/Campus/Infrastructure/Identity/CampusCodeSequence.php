<?php

declare(strict_types=1);

namespace App\Modules\Campus\Infrastructure\Identity;

use App\Modules\Campus\Domain\Contracts\CampusCodeSequenceInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final readonly class CampusCodeSequence implements CampusCodeSequenceInterface
{
    private const TABLE = 'campus_code_sequences';

    private const MAXIMUM_VALUE = 999;

    public function next(string $institutionId): int
    {
        if (trim($institutionId) === '') {
            throw new RuntimeException(
                'Campus code sequence requires an institution id.'
            );
        }

        return DB::transaction(function () use ($institutionId): int {
            $this->initializeIfMissing($institutionId);

            $sequence = DB::table(self::TABLE)
                ->where('institution_id', $institutionId)
                ->lockForUpdate()
                ->first(['current_value']);

            if ($sequence === null) {
                throw new RuntimeException(
                    'Campus code sequence could not be initialized.'
                );
            }

            $currentValue = $sequence->current_value;

            if (
                ! is_int($currentValue)
                && ! is_numeric($currentValue)
            ) {
                throw new RuntimeException(
                    'Campus code sequence returned an invalid value.'
                );
            }

            $currentValue = (int) $currentValue;

            if ($currentValue >= self::MAXIMUM_VALUE) {
                throw new RuntimeException(
                    'Campus code sequence has reached the maximum value of 999.'
                );
            }

            $nextValue = $currentValue + 1;

            $updated = DB::table(self::TABLE)
                ->where('institution_id', $institutionId)
                ->where('current_value', $currentValue)
                ->update([
                    'current_value' => $nextValue,
                    'updated_at' => now(),
                ]);

            if ($updated !== 1) {
                throw new RuntimeException(
                    'Campus code sequence could not be incremented.'
                );
            }

            return $nextValue;
        });
    }

    private function initializeIfMissing(string $institutionId): void
    {
        $exists = DB::table(self::TABLE)
            ->where('institution_id', $institutionId)
            ->exists();

        if ($exists) {
            return;
        }

        try {
            DB::table(self::TABLE)->insert([
                'institution_id' => $institutionId,
                'current_value' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (QueryException $exception) {
            if (! $this->isDuplicateKeyException($exception)) {
                throw $exception;
            }

            // Otro proceso pudo inicializar la misma secuencia.
            // La lectura bloqueante posterior verificará la fila.
        }
    }

    private function isDuplicateKeyException(
        QueryException $exception,
    ): bool {
        return (int) ($exception->errorInfo[1] ?? 0) === 1062;
    }
}
