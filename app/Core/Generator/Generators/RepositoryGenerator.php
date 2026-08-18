<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\Builders\RepositoryBuilder;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;

final class RepositoryGenerator extends BaseGenerator
{
    private const STUB = 'repository.stub';

    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
        private readonly RepositoryBuilder $builder,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
        );
    }

    public function supports(ModuleData $module): bool
    {
        return true;
    }

    public function generate(ModuleData $module): GeneratorResult
    {
        return $this->generateResult(
            self::STUB,
            $module->repositoryPath(),
            $this->builder->build($module)
        );
    }
}
