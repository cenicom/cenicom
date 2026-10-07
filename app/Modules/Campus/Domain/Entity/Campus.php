<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\Entity;

use InvalidArgumentException;

final class Campus
{
    public function __construct(
        private readonly string $id,
        private readonly string $institutionId,
        private readonly string $code,
        private readonly string $shortCode,
        private readonly string $name,
        private readonly string $addressId,
        private readonly ?string $ministryCode = null,
        private string $status = 'draft',
    ) {
        if ($id === '') {
            throw new InvalidArgumentException(
                'Campus id cannot be empty.'
            );
        }

        if ($institutionId === '') {
            throw new InvalidArgumentException(
                'Campus institution id cannot be empty.'
            );
        }

        if ($code === '') {
            throw new InvalidArgumentException(
                'Campus code cannot be empty.'
            );
        }

        if (!preg_match('/^[A-Z]{6,15}$/', $shortCode)) {
            throw new InvalidArgumentException(
                'Campus short code must contain only uppercase letters A-Z and be between 6 and 15 characters.'
            );
        }

        if ($name === '') {
            throw new InvalidArgumentException(
                'Campus name cannot be empty.'
            );
        }

        if ($addressId === '') {
            throw new InvalidArgumentException(
                'Campus address id cannot be empty.'
            );
        }

        if (!in_array($status, ['draft', 'active', 'inactive'], true)) {
            throw new InvalidArgumentException(
                'Campus status must be draft, active, or inactive.'
            );
        }
    }

    public function id(): string
    {
        return $this->id;
    }

    public function institutionId(): string
    {
        return $this->institutionId;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function shortCode(): string
    {
        return $this->shortCode;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function addressId(): string
    {
        return $this->addressId;
    }

    public function ministryCode(): ?string
    {
        return $this->ministryCode;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function activate(): void
    {
        if ($this->status === 'draft' || $this->status === 'inactive') {
            $this->status = 'active';

            return;
        }

        if ($this->status === 'active') {
            return;
        }

        throw new InvalidArgumentException(
            'Campus cannot be activated from its current status.'
        );
    }

    public function deactivate(): void
    {
        if ($this->status !== 'active') {
            throw new InvalidArgumentException(
                'Only an active campus can be deactivated.'
            );
        }

        $this->status = 'inactive';
    }
}
