<?php

declare(strict_types=1);

namespace App\Core\Module\Diagnostics;

enum StageStatus: string
{
    case Pending = 'pending';

    case Running = 'running';

    case Success = 'success';

    case Failed = 'failed';

    case Skipped = 'skipped';

    public function isFinal(): bool
    {
        return match ($this) {
            self::Success,
            self::Failed,
            self::Skipped => true,

            default => false,
        };
    }

    public function isFailure(): bool
    {
        return $this === self::Failed;
    }

    public function isRunning(): bool
    {
        return $this === self::Running;
    }
}
