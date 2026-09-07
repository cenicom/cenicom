<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Presentation\Renderers;

use App\Core\Generator\Presentation\DTO\ComponentMetadata;
use App\Core\Generator\Presentation\DTO\InputPresentation;
use App\Core\Generator\Presentation\Renderers\ComponentRenderer;
use App\Core\Generator\Support\StubManager;
use Tests\Support\GeneratorTestCase;

final class ComponentRendererTest extends GeneratorTestCase
{
    public function test_renders_datetime_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'created_at',
            label: 'Created at',
            type: 'datetime-local',
            placeholder: '',
            component: new ComponentMetadata(
                component: 'datetime',
                bladeComponent: 'x-cn.datetime',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '$model->created_at',
                icon: '',
                placeholder: '',
            ),
            required: false,
            readonly: false,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            'x-cn.datetime',
            $result,
        );
    }
}
