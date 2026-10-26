<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\DTO;

final readonly class AcademicPeriodData
{
    public function __construct(
        public string $code,
        public string $name,
        public int $sequenceNumber,
        public string $startDate,
        public string $endDate,
        public ?string $declaredDuration = null,
        public ?string $durationUnit = null,
    ) {
    }

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
    public function toArray(): array
    {
        return [
            'code' => trim($this->code),
            'name' => trim($this->name),
            'sequence_number' => $this->sequenceNumber,
            'start_date' => trim($this->startDate),
            'end_date' => trim($this->endDate),
            'declared_duration' => $this->declaredDuration === null
                ? null
                : trim($this->declaredDuration),
            'duration_unit' => $this->durationUnit === null
                ? null
                : trim($this->durationUnit),
        ];
    }
}
