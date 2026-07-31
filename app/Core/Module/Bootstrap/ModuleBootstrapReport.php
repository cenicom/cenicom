<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;

final class ModuleBootstrapReport
{
    private array $registered = [];

    private array $skipped = [];

    private array $failed = [];

    private ModuleBootstrapMetrics $metrics;

    public function __construct()
    {
        $this->metrics = new ModuleBootstrapMetrics();
    }

    //Getter
    public function metrics(): ModuleBootstrapMetrics
    {
        return $this->metrics;
    }


    public function addRegistered(
        string $moduleName,
        array $providers
    ): void {
        $this->registered[] = [
            'module' => $moduleName,
            'providers' => $providers,
        ];
    }


    public function addSkipped(
        string $moduleName,
        string $reason
    ): void {
        $this->skipped[] = [
            'module' => $moduleName,
            'reason' => $reason,
        ];
    }


    public function addFailed(
        string $moduleName,
        \Throwable $exception
    ): void {
        $this->failed[] = [
            'module' => $moduleName,
            'exception' => $exception,
        ];
    }


    public function registered(): array
    {
        return $this->registered;
    }


    public function skipped(): array
    {
        return $this->skipped;
    }


    public function failed(): array
    {
        return $this->failed;
    }
}
