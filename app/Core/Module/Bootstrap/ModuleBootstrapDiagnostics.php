<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;

use Throwable;

final class ModuleBootstrapDiagnostics
{
    private ?string $manifestPath = null;

    private ?string $moduleName = null;

    private ?string $failedStage = null;

    private ?Throwable $exception = null;


    public function setManifestPath(
        string $manifestPath
    ): void {
        $this->manifestPath = $manifestPath;
    }


    public function manifestPath(): ?string
    {
        return $this->manifestPath;
    }


    public function setModuleName(
        string $moduleName
    ): void {
        $this->moduleName = $moduleName;
    }


    public function moduleName(): ?string
    {
        return $this->moduleName;
    }


    public function setFailedStage(
        string $stage
    ): void {
        $this->failedStage = $stage;
    }


    public function failedStage(): ?string
    {
        return $this->failedStage;
    }


    public function setException(
        Throwable $exception
    ): void {
        $this->exception = $exception;
    }


    public function exception(): ?Throwable
    {
        return $this->exception;
    }


    public function hasFailure(): bool
    {
        return $this->exception !== null;
    }
}
