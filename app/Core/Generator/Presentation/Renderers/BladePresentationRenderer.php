<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Renderers;

use App\Core\Generator\Presentation\Contracts\PresentationRendererInterface;
use App\Core\Generator\Presentation\DTO\InputPresentation;
use App\Core\Generator\Support\StubManager;

final class BladePresentationRenderer implements PresentationRendererInterface
{
    public function __construct(
        private readonly StubManager $stubManager
    ) {
    }


    /**
     * Renderiza una colección de presentaciones
     * como código Blade.
     *
     * @param InputPresentation[] $presentations
     */
    public function render(
        array $presentations
    ): string {

        $fields = [];

        foreach ($presentations as $presentation) {

            $fields[] = $this->renderPresentation(
                $presentation
            );

        }

        return implode(
            PHP_EOL . PHP_EOL,
            $fields
        );
    }


    private function renderPresentation(
        InputPresentation $presentation
    ): string {

        $stub = $this->resolveStub(
            $presentation
        );


        return $this->stubManager->render(
            $stub,
            $this->buildVariables($presentation)
        );
    }


    private function resolveStub(
        InputPresentation $presentation
    ): string {

        return match ($presentation->type) {

            'input' =>
                'components/input.stub',

            'select' =>
                'components/select.stub',

            'textarea' =>
                'components/textarea.stub',

            default =>
                throw new \RuntimeException(
                    "Unsupported presentation type: {$presentation->type}"
                ),
        };
    }


    private function buildVariables(
        InputPresentation $presentation
    ): array {

        return [

            'name' =>
                $presentation->name,

            'label' =>
                $presentation->label,

            'placeholder' =>
                $presentation->placeholder,

            'column_class' =>
                'col-md-6',

        ];
    }
}
