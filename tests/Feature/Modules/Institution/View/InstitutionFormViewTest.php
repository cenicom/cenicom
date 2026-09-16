<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Institution\View;

use App\Modules\Institution\Models\Institution;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

final class InstitutionFormViewTest extends TestCase
{
    public function test_form_renders_institution_fields_with_cn_ui_components(): void
    {

        $institution = new Institution([
            'name' => 'Institución Educativa Nacional',
            'official_registration_country' => 'CO',
            'official_registration_authority' => 'Ministerio de Educación',
            'official_registration_value' => 'REG-001',
        ]);

        $this->withViewErrors([]);

        $view = View::make(
            'institutions::_form',
            compact('institution')
        );

        $html = $view->render();

        //$html = $view->render();

        self::assertStringContainsString('cn-group', $html);
        self::assertStringContainsString('cn-group-2', $html);
        self::assertStringContainsString('cn-field', $html);
        self::assertStringContainsString('cn-label', $html);
        self::assertStringContainsString('cn-input', $html);
        self::assertStringContainsString('cn-help', $html);
        //self::assertStringContainsString('cn-error', $html);

        self::assertStringContainsString(
            'name="name"',
            $html
        );

        self::assertStringContainsString(
            'name="officialRegistration[country]"',
            $html
        );

        self::assertStringContainsString(
            'name="officialRegistration[authority]"',
            $html
        );

        self::assertStringContainsString(
            'name="officialRegistration[value]"',
            $html
        );

        self::assertStringContainsString(
            'value="Institución Educativa Nacional"',
            $html
        );

        self::assertStringContainsString(
            'value="CO"',
            $html
        );

        self::assertStringContainsString(
            'value="Ministerio de Educación"',
            $html
        );

        self::assertStringContainsString(
            'value="REG-001"',
            $html
        );
    }

    public function test_form_uses_laravel_validation_keys_for_nested_registration_fields(): void
    {
        $institution = new Institution([
            'name' => 'Institución Educativa Nacional',
            'official_registration_country' => 'CO',
            'official_registration_authority' => 'Ministerio de Educación',
            'official_registration_value' => 'REG-001',
        ]);

        $view = $this
            ->withViewErrors([
                'name' => 'El nombre es obligatorio.',
                'officialRegistration.country' => 'El país es obligatorio.',
                'officialRegistration.authority' => 'La autoridad es obligatoria.',
                'officialRegistration.value' => 'El valor es obligatorio.',
            ])
            ->view(
                'institutions::_form',
                compact('institution')
            );

        $view->assertSee('is-invalid', false);
        $view->assertSee('aria-invalid="true"', false);

        $view->assertSee('El nombre es obligatorio.');
        $view->assertSee('El país es obligatorio.');
        $view->assertSee('La autoridad es obligatoria.');
        $view->assertSee('El valor es obligatorio.');

        $view->assertSee(
            'id="officialRegistration.country-error"',
            false
        );

        $view->assertSee(
            'id="officialRegistration.authority-error"',
            false
        );

        $view->assertSee(
            'id="officialRegistration.value-error"',
            false
        );
    }

    public function test_form_does_not_expose_non_editable_institution_attributes(): void
    {
        $institution = new Institution([
            'id' => '01J00000000000000000000000',
            'code' => 'INST-001',
            'name' => 'Institución Educativa Nacional',
            'official_registration_country' => 'CO',
            'official_registration_authority' => 'Ministerio de Educación',
            'official_registration_value' => 'REG-001',
            'status' => 'active',
        ]);

        $view = $this
            ->withViewErrors([])
            ->view(
                'institutions::_form',
                compact('institution')
            );
        $view->assertDontSee(
            'name="code"',
            false
        );

        $view->assertDontSee(
            'name="status"',
            false
        );

        $view->assertDontSee(
            'officialRegistration->',
            false
        );
    }
}
