<?php

declare(strict_types=1);

namespace App\Modules\Institution\Infrastructure\Identity;

use App\Modules\Institution\Domain\Contracts\InstitutionCodeGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionCodeSequenceInterface;
use InvalidArgumentException;

final readonly class InstitutionCodeGenerator implements InstitutionCodeGeneratorInterface
{
    public function __construct(
        private InstitutionCodeSequenceInterface $sequence,
    ) {
    }

    public function generate(): string
    {
        $value = $this->sequence->next();

        if ($value <= 0) {
            throw new InvalidArgumentException(
                'Institution code sequence must be greater than zero.'
            );
        }

        return sprintf(
            'CEN-%06d',
            $value
        );
    }
}
