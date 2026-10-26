<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\DTO;

final readonly class AcademicCalendarEventData
{
    public function __construct(
        public string $code,
        public string $eventType,
        public string $title,
        public string $audience,
        public string $startDate,
        public string $endDate,
        public ?string $academicPeriodId = null,
        public bool $isOfficiallyRequired = false,
        public bool $activationPrerequisite = false,
        public bool $closurePrerequisite = false,
        public ?string $sourceReference = null,
    ) {
    }

    /** @return array<string, string|bool|null> */
    public function toArray(): array
    {
        return [
            'academic_period_id' => $this->academicPeriodId,
            'code' => $this->code,
            'event_type' => $this->eventType,
            'title' => $this->title,
            'audience' => $this->audience,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'is_officially_required' => $this->isOfficiallyRequired,
            'activation_prerequisite' => $this->activationPrerequisite,
            'closure_prerequisite' => $this->closurePrerequisite,
            'source_reference' => $this->sourceReference,
        ];
    }
}
