<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\Entity;

use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use InvalidArgumentException;

final readonly class Institution
{
    private const VALID_STATUSES = ['draft', 'active', 'inactive'];

    public function __construct(
        private string $id,
        private string $name,
        private string $code,
        private string $shortCode,
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

        if (trim($this->shortCode) === '') {
            throw new InvalidArgumentException(
                'Institution short code cannot be empty.'
            );
        }

        if (!preg_match('/^[A-Z]{6,15}$/', $this->shortCode)) {
            throw new InvalidArgumentException(
                'Institution short code must contain only uppercase letters A-Z and be between 6 and 15 characters.'
            );
        }

        if (!in_array($this->status, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException(
                'Institution status must be draft, active, or inactive.'
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

    public function shortCode(): string
    {
        return $this->shortCode;
    }

    public function officialRegistration(): ?InstitutionOfficialRegistration
    {
        return $this->officialRegistration;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function activate(): self
    {
        if (!in_array($this->status, ['draft', 'inactive'], true)) {
            throw new InvalidArgumentException(
                'Institution cannot be activated from its current status.'
            );
        }

        return $this->withStatus('active');
    }

    public function deactivate(): self
    {
        if ($this->status !== 'active') {
            throw new InvalidArgumentException(
                'Only an active institution can be deactivated.'
            );
        }

        return $this->withStatus('inactive');
    }

    private function withStatus(string $status): self
    {
        return new self(
            id: $this->id,
            name: $this->name,
            code: $this->code,
            shortCode: $this->shortCode,
            officialRegistration: $this->officialRegistration,
            status: $status,
        );
    }
}
