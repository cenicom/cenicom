<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;

final class ModuleBootstrapMetrics
{
    private ?float $startedAt = null;

    private ?float $completedAt = null;

    private int $registered = 0;

    private int $skipped = 0;

    private int $failed = 0;


    public function start(): void
    {
        $this->startedAt = microtime(true);
    }


    public function complete(): void
    {
        $this->completedAt = microtime(true);
    }


    public function startedAt(): ?float
    {
        return $this->startedAt;
    }


    public function completedAt(): ?float
    {
        return $this->completedAt;
    }


    public function duration(): ?float
    {
        if (
            $this->startedAt === null ||
            $this->completedAt === null
        ) {
            return null;
        }

        return $this->completedAt - $this->startedAt;
    }


    public function incrementRegistered(): void
    {

        $this->registered++;

    }


    public function incrementSkipped(): void
    {
        $this->skipped++;
    }


    public function incrementFailed(): void
    {
        $this->failed++;
    }


    public function registered(): int
    {
        return $this->registered;
    }


    public function skipped(): int
    {
        return $this->skipped;
    }


    public function failed(): int
    {
        return $this->failed;
    }
}
