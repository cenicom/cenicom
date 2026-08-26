<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\Builders\SeederBuilder;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;

final class SeederGenerator extends BaseGenerator
{
    private const STUB = 'seeder.stub';

    private readonly SeederBuilder $builder;

    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
        ?SeederBuilder $builder = null,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
        );

        $this->builder = $builder ?? new SeederBuilder();
    }

    public function supports(ModuleData $module): bool
    {
        return true;
    }

    public function generate(
        ModuleData $module
    ): GeneratorResult {
        return $this->generateResult(
            self::STUB,
            $module->seederPath(),
            $this->builder->build($module),
        );
    }
}
