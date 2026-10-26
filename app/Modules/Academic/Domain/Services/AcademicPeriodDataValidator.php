<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Modules\Academic\Domain\DTO\AcademicPeriodData;
use DomainException;
use Illuminate\Support\Str;
use stdClass;

final class AcademicPeriodDataValidator
{
    /**
     * @return array{
     *     code: string,
     *     name: string,
     *     sequence_number: int,
     *     start_date: string,
     *     end_date: string,
     *     declared_duration: string|null,
     *     duration_unit: string|null
     * }
     */
    public function validate(AcademicPeriodData $data, stdClass $version): array
    {
        $values = $data->toArray();

        if ($values['code'] === '' || Str::length($values['code']) > 20) {
            throw new DomainException('El código del período es obligatorio y admite hasta 20 caracteres.');
        }

        if ($values['name'] === '' || Str::length($values['name']) > 100) {
            throw new DomainException('El nombre del período es obligatorio y admite hasta 100 caracteres.');
        }

        if ($values['sequence_number'] < 1 || $values['sequence_number'] > 65535) {
            throw new DomainException('La secuencia del período debe estar entre 1 y 65535.');
        }

        foreach (['start_date', 'end_date'] as $field) {
            if (! $this->isValidDate($values[$field])) {
                throw new DomainException("La fecha {$field} debe tener formato válido YYYY-MM-DD.");
            }
        }

        if ($values['start_date'] > $values['end_date']) {
            throw new DomainException('La fecha de inicio del período no puede ser posterior a su fecha de fin.');
        }

        $officialStart = $version->calendar_start_date;
        $officialEnd = $version->calendar_end_date;

        if (
            $officialStart === null
            || $officialEnd === null
            || $values['start_date'] < (string) $officialStart
            || $values['end_date'] > (string) $officialEnd
        ) {
            throw new DomainException('El período debe quedar dentro del intervalo oficial de su versión de publicación.');
        }

        $duration = $values['declared_duration'];
        $unit = $values['duration_unit'];

        if (($duration === null) !== ($unit === null)) {
            throw new DomainException('La duración declarada y su unidad deben informarse juntas o dejarse ambas vacías.');
        }

        if ($duration !== null) {
            if (
                preg_match('/^\d{1,4}(?:\.\d{1,2})?$/', $duration) !== 1
                || (float) $duration <= 0
                || ! in_array($unit, ['DAYS', 'WEEKS'], true)
            ) {
                throw new DomainException('La duración declarada debe ser positiva y usar la unidad DAYS o WEEKS.');
            }
        }

        return $values;
    }

    private function isValidDate(string $date): bool
    {
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches) !== 1) {
            return false;
        }

        return checkdate(
            (int) $matches[2],
            (int) $matches[3],
            (int) $matches[1],
        );
    }
}
