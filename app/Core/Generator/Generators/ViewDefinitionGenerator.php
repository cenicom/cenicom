<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;

final class ViewDefinitionGenerator extends BaseGenerator
{
    private const STUB = 'view-definition.stub';

    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
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
            $module->viewDefinitionPath(),
            [
                'viewDefinitionNamespace' => $module->viewDefinitionNamespace(),
                'viewDefinitionClass' => $module->viewDefinitionClass(),
                'viewPrefix' => $module->viewPrefix(),
                'viewPath' => 'app/Modules/' . $module->name() . '/Resources/Views',
            ],
        );
    }
}
