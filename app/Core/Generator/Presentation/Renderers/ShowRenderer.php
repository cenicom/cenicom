<?php

declare(strict_types=1);

namespace App\Core\Generator\Presentation\Renderers;

use App\Core\Generator\Presentation\DTO\ShowFieldPresentation;
use App\Core\Generator\Presentation\DTO\ShowPresentation;
use App\Core\Generator\Support\StubManager;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Renderiza la vista Show utilizando stubs Blade.
 *
 * Transforma un ShowPresentation en código Blade.
 *
 * No conoce ModuleData.
 * No contiene lógica de negocio.
 * No escribe archivos.
 *
 * @package App\Core\Generator\Presentation\Renderers
 * @since 2.0.0
 */
final readonly class ShowRenderer
{
    /**
     * Stub utilizado para cada campo.
     */
    private const STUB_FIELD = 'components/show/field.stub';

    public function __construct(
        private StubManager $stubManager,
    ) {}

    /**
     * Renderiza todos los campos.
     */
    public function render(
        ShowPresentation $presentation,
    ): string {

        $fields = [];

        foreach ($presentation->fields() as $field) {

            $fields[] = $this->renderField(
                $field,
            );
        }

        return implode(
            PHP_EOL . PHP_EOL,
            $fields,
        );
    }

    /**
     * Renderiza un campo.
     */
    private function renderField(
        ShowFieldPresentation $field,
    ): string {

        return $this->stubManager->render(
            self::STUB_FIELD,
            [
                'name' => $field->name,
                'label' => $field->label,
                'binding' => $field->binding,
                'css_class' => $field->cssClass,
                'column_class' => $field->columnClass,
            ],
        );
    }
}
