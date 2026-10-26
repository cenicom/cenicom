<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\Services;

use App\Modules\Academic\Domain\DTO\AcademicCalendarEventData;
use DomainException;
use Illuminate\Support\Str;
use stdClass;

final class AcademicCalendarEventDataValidator
{
    private const EVENT_TYPES = [
        'STUDENT_CLASSES_START',
        'STUDENT_CLASSES_END',
        'STUDENT_RECESS',
        'INSTITUTIONAL_PREPARATION',
        'INSTITUTIONAL_DEVELOPMENT',
        'TEACHER_VACATION',
        'EXAM_INTERVAL',
        'PEDAGOGICAL_DAY',
        'OTHER',
    ];

    private const AUDIENCES = ['STUDENTS', 'TEACHERS', 'ADMINISTRATORS', 'ALL'];

    /**
     * @return array{
     *   academic_period_id: string|null,
     *   code: string,
     *   event_type: string,
     *   title: string,
     *   audience: string,
     *   start_date: string,
     *   end_date: string,
     *   is_officially_required: bool,
     *   activation_prerequisite: bool,
     *   closure_prerequisite: bool,
     *   source_reference: string|null
     * }
     */
    public function validate(
        AcademicCalendarEventData $data,
        stdClass $version,
        ?stdClass $period,
    ): array {
        $values = $data->toArray();
        $values['code'] = trim($values['code']);
        $values['title'] = trim($values['title']);
        $values['event_type'] = trim($values['event_type']);
        $values['audience'] = trim($values['audience']);
        $values['source_reference'] = $values['source_reference'] === null
            ? null
            : trim($values['source_reference']);

        if ($values['code'] === '' || Str::length($values['code']) > 40) {
            throw new DomainException('El código del evento es obligatorio y admite hasta 40 caracteres.');
        }

        if (! in_array($values['event_type'], self::EVENT_TYPES, true)) {
            throw new DomainException('El tipo de evento no está permitido por el calendario académico.');
        }

        if ($values['title'] === '' || Str::length($values['title']) > 180) {
            throw new DomainException('El título del evento es obligatorio y admite hasta 180 caracteres.');
        }

        if (! in_array($values['audience'], self::AUDIENCES, true)) {
            throw new DomainException('La audiencia del evento no está permitida.');
        }

        if ($values['source_reference'] === '') {
            $values['source_reference'] = null;
        }
        if ($values['source_reference'] !== null && Str::length($values['source_reference']) > 200) {
            throw new DomainException('La referencia de fuente admite hasta 200 caracteres.');
        }

        foreach (['start_date', 'end_date'] as $field) {
            if (! $this->isValidDate($values[$field])) {
                throw new DomainException("La fecha {$field} debe tener formato válido YYYY-MM-DD.");
            }
        }

        if ($values['start_date'] > $values['end_date']) {
            throw new DomainException('La fecha de inicio del evento no puede ser posterior a su fecha de fin.');
        }

        $officialStart = $version->calendar_start_date;
        $officialEnd = $version->calendar_end_date;
        if (
            $officialStart === null
            || $officialEnd === null
            || $values['start_date'] < (string) $officialStart
            || $values['end_date'] > (string) $officialEnd
        ) {
            throw new DomainException('El evento debe quedar dentro del intervalo oficial de su versión de publicación.');
        }

        if ($values['event_type'] === 'EXAM_INTERVAL' && $values['academic_period_id'] === null) {
            throw new DomainException('Un intervalo de exámenes debe pertenecer a un período académico.');
        }

        if ($values['academic_period_id'] !== null) {
            if ($period === null) {
                throw new DomainException('El período indicado no existe o no pertenece a la misma versión oficial.');
            }

            if (
                $values['start_date'] < (string) $period->start_date
                || $values['end_date'] > (string) $period->end_date
            ) {
                throw new DomainException('El evento debe quedar dentro de las fechas de su período académico.');
            }
        }

        return $values;
    }

    private function isValidDate(string $date): bool
    {
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches) !== 1) {
            return false;
        }

        return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }
}
