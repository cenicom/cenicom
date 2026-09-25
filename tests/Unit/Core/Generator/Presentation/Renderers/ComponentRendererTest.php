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
                bladeComponent: 'x-cn.forms.datetime',
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
            'x-cn.forms.datetime',
            $result,
        );
    }

    public function test_renders_required_and_readonly_attributes(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'name',
            label: 'Name',
            type: 'text',
            placeholder: 'Name',
            component: new ComponentMetadata(
                component: 'input',
                bladeComponent: 'x-cn.forms.input',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '$model->name',
                icon: '',
                placeholder: 'Name',
            ),
            required: true,
            readonly: true,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':required="true"',
            $result,
        );

        self::assertStringContainsString(
            ':readonly="true"',
            $result,
        );
    }

    public function test_renders_select_required_and_placeholder(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'status',
            label: 'Status',
            type: 'select',
            placeholder: 'Select status',
            component: new ComponentMetadata(
                component: 'select',
                bladeComponent: 'x-cn.forms.select',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-list',
                placeholder: 'Select status',
            ),
            required: true,
            readonly: false,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            'placeholder="Select status"',
            $result,
        );

        self::assertStringContainsString(
            ':required="true"',
            $result,
        );
    }

    public function test_renders_checkbox_required(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'active',
            label: 'Active',
            type: 'checkbox',
            placeholder: '',
            component: new ComponentMetadata(
                component: 'checkbox',
                bladeComponent: 'x-cn.forms.checkbox',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-check-square',
                placeholder: '',
            ),
            required: true,
            readonly: false,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':required="true"',
            $result,
        );
    }

    public function test_renders_date_required_and_readonly(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'birth_date',
            label: 'Birth Date',
            type: 'date',
            placeholder: 'Enter Birth Date',
            component: new ComponentMetadata(
                component: 'date',
                bladeComponent: 'x-cn.forms.date',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-calendar',
                placeholder: 'Enter Birth Date',
            ),
            required: true,
            readonly: true,
            disabled: false,
        );

        $result = $renderer->render($input);


        self::assertStringContainsString(
            ':required="true"',
            $result,
        );

        self::assertStringContainsString(
            ':readonly="true"',
            $result,
        );
    }

    public function test_renders_datetime_required_and_readonly(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'start_at',
            label: 'Start At',
            type: 'datetime-local',
            placeholder: 'Enter Start At',
            component: new ComponentMetadata(
                component: 'datetime',
                bladeComponent: 'x-cn.forms.datetime',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-calendar-event',
                placeholder: 'Enter Start At',
            ),
            required: true,
            readonly: true,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':required="true"',
            $result,
        );

        self::assertStringContainsString(
            ':readonly="true"',
            $result,
        );
    }

    public function test_renders_number_required_and_readonly(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'amount',
            label: 'Amount',
            type: 'number',
            placeholder: 'Enter Amount',
            component: new ComponentMetadata(
                component: 'number',
                bladeComponent: 'x-cn.forms.number',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-123',
                placeholder: 'Enter Amount',
            ),
            required: true,
            readonly: true,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':required="true"',
            $result,
        );

        self::assertStringContainsString(
            ':readonly="true"',
            $result,
        );
    }

    public function test_renders_email_required(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'email',
            label: 'Email',
            type: 'email',
            placeholder: 'Enter Email',
            component: new ComponentMetadata(
                component: 'email',
                bladeComponent: 'x-cn.forms.email',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-envelope',
                placeholder: 'Enter Email',
            ),
            required: true,
            readonly: false,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':required="true"',
            $result,
        );
    }

    public function test_renders_email_readonly(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'email',
            label: 'Email',
            type: 'email',
            placeholder: 'Enter Email',
            component: new ComponentMetadata(
                component: 'email',
                bladeComponent: 'x-cn.forms.email',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-envelope',
                placeholder: 'Enter Email',
            ),
            required: false,
            readonly: true,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':readonly="true"',
            $result,
        );
    }

    public function test_renders_password_required(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'password',
            label: 'Password',
            type: 'password',
            placeholder: 'Enter Password',
            component: new ComponentMetadata(
                component: 'password',
                bladeComponent: 'x-cn.forms.password',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-key',
                placeholder: 'Enter Password',
            ),
            required: true,
            readonly: false,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            '<x-cn.forms.password',
            $result,
        );

        self::assertStringContainsString(
            ':required="true"',
            $result,
        );
    }

    public function test_renders_password_readonly(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'password',
            label: 'Password',
            type: 'password',
            placeholder: 'Enter Password',
            component: new ComponentMetadata(
                component: 'password',
                bladeComponent: 'x-cn.forms.password',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-key',
                placeholder: 'Enter Password',
            ),
            required: false,
            readonly: true,
            disabled: false,
        );

        $result = $renderer->render($input);


        self::assertStringContainsString(
            ':readonly="true"',
            $result,
        );
    }

    public function test_renders_textarea_required(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'description',
            label: 'Description',
            type: 'text',
            placeholder: 'Enter Description',
            component: new ComponentMetadata(
                component: 'textarea',
                bladeComponent: 'x-cn.forms.textarea',
                cssClass: '',
                columnClass: 'col-md-12',
                binding: '',
                icon: 'bi-card-text',
                placeholder: 'Enter Description',
            ),
            required: true,
            readonly: false,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            '<x-cn.forms.textarea',
            $result,
        );

        self::assertStringContainsString(
            ':required="true"',
            $result,
        );
    }

    public function test_renders_textarea_readonly(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'description',
            label: 'Description',
            type: 'text',
            placeholder: 'Enter Description',
            component: new ComponentMetadata(
                component: 'textarea',
                bladeComponent: 'x-cn.forms.textarea',
                cssClass: '',
                columnClass: 'col-md-12',
                binding: '',
                icon: 'bi-card-text',
                placeholder: 'Enter Description',
            ),
            required: false,
            readonly: true,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            '<x-cn.forms.textarea',
            $result,
        );

        self::assertStringContainsString(
            ':readonly="true"',
            $result,
        );
    }

    public function test_renders_default_value_for_input_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'status',
            label: 'Status',
            type: 'text',
            placeholder: 'Enter Status',
            component: new ComponentMetadata(
                component: 'input',
                bladeComponent: 'x-cn.forms.input',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-type',
                placeholder: 'Enter Status',
            ),
            required: false,
            readonly: false,
            disabled: false,
            default: 'active',
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':value="\'active\'"',
            $result,
        );
    }

    public function test_renders_boolean_default_value_for_input_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'active',
            label: 'Active',
            type: 'text',
            placeholder: 'Enter Active',
            component: new ComponentMetadata(
                component: 'input',
                bladeComponent: 'x-cn.forms.input',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-type',
                placeholder: 'Enter Active',
            ),
            required: false,
            readonly: false,
            disabled: false,
            default: true,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':value="true"',
            $result,
        );
    }

    public function test_renders_integer_default_value_for_input_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'order',
            label: 'Order',
            type: 'number',
            placeholder: 'Enter Order',
            component: new ComponentMetadata(
                component: 'number',
                bladeComponent: 'x-cn.forms.number',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-123',
                placeholder: 'Enter Order',
            ),
            required: false,
            readonly: false,
            disabled: false,
            default: 0,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':value="0"',
            $result,
        );
    }

    public function test_renders_default_value_for_date_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'birth_date',
            label: 'Birth Date',
            type: 'date',
            placeholder: 'Enter Birth Date',
            component: new ComponentMetadata(
                component: 'date',
                bladeComponent: 'x-cn.forms.date',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-calendar',
                placeholder: 'Enter Birth Date',
            ),
            required: false,
            readonly: false,
            disabled: false,
            default: '2026-09-14',
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':value="\'2026-09-14\'"',
            $result,
        );
    }

    public function test_renders_default_value_for_datetime_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'start_at',
            label: 'Start At',
            type: 'datetime-local',
            placeholder: 'Enter Start At',
            component: new ComponentMetadata(
                component: 'datetime',
                bladeComponent: 'x-cn.forms.datetime',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-calendar-event',
                placeholder: 'Enter Start At',
            ),
            required: false,
            readonly: false,
            disabled: false,
            default: '2026-09-14T10:30',
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':value="\'2026-09-14T10:30\'"',
            $result,
        );
    }

    public function test_renders_default_value_for_email_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'email',
            label: 'Email',
            type: 'email',
            placeholder: 'Enter Email',
            component: new ComponentMetadata(
                component: 'email',
                bladeComponent: 'x-cn.forms.email',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-envelope',
                placeholder: 'Enter Email',
            ),
            required: false,
            readonly: false,
            disabled: false,
            default: 'test@example.com',
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':value="\'test@example.com\'"',
            $result,
        );
    }

    public function test_renders_default_value_for_password_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'password',
            label: 'Password',
            type: 'password',
            placeholder: 'Enter Password',
            component: new ComponentMetadata(
                component: 'password',
                bladeComponent: 'x-cn.forms.password',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-key',
                placeholder: 'Enter Password',
            ),
            required: false,
            readonly: false,
            disabled: false,
            default: 'secret',
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':value="\'secret\'"',
            $result,
        );
    }

    public function test_renders_default_value_for_textarea_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'description',
            label: 'Description',
            type: 'text',
            placeholder: 'Enter Description',
            component: new ComponentMetadata(
                component: 'textarea',
                bladeComponent: 'x-cn.forms.textarea',
                cssClass: '',
                columnClass: 'col-md-12',
                binding: '',
                icon: 'bi-card-text',
                placeholder: 'Enter Description',
            ),
            required: false,
            readonly: false,
            disabled: false,
            default: 'Descripción',
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':value="\'Descripción\'"',
            $result,
        );
    }

    public function test_renders_maxlength_for_input_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'code',
            label: 'Code',
            type: 'text',
            placeholder: 'Enter Code',
            component: new ComponentMetadata(
                component: 'input',
                bladeComponent: 'x-cn.forms.input',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-type',
                placeholder: 'Enter Code',
                attributes: [
                    'maxlength' => 20,
                ],
            ),
            required: false,
            readonly: false,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':maxlength="20"',
            $result,
        );
    }

    public function test_renders_maxlength_for_email_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'email',
            label: 'Email',
            type: 'email',
            placeholder: 'Enter Email',
            component: new ComponentMetadata(
                component: 'email',
                bladeComponent: 'x-cn.forms.email',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-envelope',
                placeholder: 'Enter Email',
                attributes: [
                    'maxlength' => 100,
                ],
            ),
            required: false,
            readonly: false,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':maxlength="100"',
            $result,
        );
    }

    public function test_renders_maxlength_for_password_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'password',
            label: 'Password',
            type: 'password',
            placeholder: 'Enter Password',
            component: new ComponentMetadata(
                component: 'password',
                bladeComponent: 'x-cn.forms.password',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-key',
                placeholder: 'Enter Password',
                attributes: [
                    'maxlength' => 60,
                ],
            ),
            required: false,
            readonly: false,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':maxlength="60"',
            $result,
        );
    }

    public function test_renders_maxlength_for_textarea_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'description',
            label: 'Description',
            type: 'text',
            placeholder: 'Enter Description',
            component: new ComponentMetadata(
                component: 'textarea',
                bladeComponent: 'x-cn.forms.textarea',
                cssClass: '',
                columnClass: 'col-md-12',
                binding: '',
                icon: 'bi-card-text',
                placeholder: 'Enter Description',
                attributes: [
                    'maxlength' => 500,
                ],
            ),
            required: false,
            readonly: false,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':maxlength="500"',
            $result,
        );
    }

    public function test_renders_step_for_number_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'amount',
            label: 'Amount',
            type: 'number',
            placeholder: 'Enter Amount',
            component: new ComponentMetadata(
                component: 'number',
                bladeComponent: 'x-cn.forms.number',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-123',
                placeholder: 'Enter Amount',
                attributes: [
                    'step' => '0.01',
                ],
            ),
            required: false,
            readonly: false,
            disabled: false,
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':step="0.01"',
            $result,
        );
    }

    public function test_renders_default_value_for_select_component(): void
    {
        $renderer = new ComponentRenderer(
            new StubManager(),
        );

        $input = new InputPresentation(
            name: 'status',
            label: 'Status',
            type: 'select',
            placeholder: 'Enter Status',
            component: new ComponentMetadata(
                component: 'select',
                bladeComponent: 'x-cn.forms.select',
                cssClass: '',
                columnClass: 'col-md-6',
                binding: '',
                icon: 'bi-list',
                placeholder: 'Enter Status',
            ),
            required: false,
            readonly: false,
            disabled: false,
            default: 'active',
        );

        $result = $renderer->render($input);

        self::assertStringContainsString(
            ':value="\'active\'"',
            $result,
        );
    }
}
