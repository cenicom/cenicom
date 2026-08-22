<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\Entity;

use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use InvalidArgumentException;


final readonly class Institution
{
    public function __construct(
        private string $id,
        private string $name,
        private string $code,
        private ?InstitutionOfficialRegistration $officialRegistration = null,
        private string $status = 'draft',
    ) {
        if ($id === '') {
            throw new InvalidArgumentException(
                'Institution id cannot be empty.'
            );
        }
        if ($name === '') {
            throw new InvalidArgumentException(
                'Institution name cannot be empty.'
            );
        }
        if ($code === '') {
            throw new InvalidArgumentException(
                'Institution code cannot be empty.'
            );
        }
    }
    public function id(): string
    {
        return $this->id;
    }
    public function name(): string
    {
        return $this->name;
    }
    public function code(): string
    {
        return $this->code;
    }
    public function officialRegistration(): ?InstitutionOfficialRegistration
    {
        return $this->officialRegistration;
    }
    public function status(): string
    {
        return $this->status;
    }
}
