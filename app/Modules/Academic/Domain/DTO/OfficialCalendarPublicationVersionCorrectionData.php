<?php

declare(strict_types=1);

namespace App\Modules\Academic\Domain\DTO;

final readonly class OfficialCalendarPublicationVersionCorrectionData
{
    public function __construct(
        public string $authorityNameSnapshot,
        public string $title,
        public ?string $officialVersionLabel,
        public ?string $promulgatedOn,
        public ?string $calendarStartDate,
        public ?string $calendarEndDate,
        public ?string $sourceUrl,
        public ?string $sourceReference,
        public ?string $documentSha256,
    ) {
    }

    /**
     * Contenido completo de la nueva versión.
     *
     * Los campos opcionales se envían expresamente como null cuando
     * no correspondan; no se mezclan implícitamente con los anteriores.
     *
     * @return array<string, string|null>
     */
    public function toContentArray(): array
    {
        return [
            'official_version_label' => $this->officialVersionLabel,
            'authority_name_snapshot' => $this->authorityNameSnapshot,
            'title' => $this->title,
            'promulgated_on' => $this->promulgatedOn,
            'calendar_start_date' => $this->calendarStartDate,
            'calendar_end_date' => $this->calendarEndDate,
            'source_url' => $this->sourceUrl,
            'source_reference' => $this->sourceReference,
            'document_sha256' => $this->documentSha256,
        ];
    }
}
