<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline\Steps;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\Contracts\FileWriterInterface;
use Closure;
use Throwable;

final readonly class PrepareDirectoriesStep implements PipelineStepInterface
{
    public function __construct(
        private FileWriterInterface $writer,
    ) {
    }

    public function handle(
        ModuleData $module,
        GeneratorResult $result,
        Closure $next,
    ): GeneratorResult {

        try {
            foreach ($module->directories() as $directory) {
                $this->writer->ensureDirectory($directory);
            }
        } catch (Throwable $exception) {
            $result->addError(
                $exception->getMessage()
            );

            return $result;
        }

        return $next(
            $module,
            $result,
        );
    }
}
