<?php

declare(strict_types=1);

namespace App\Core\Generator\Processors;

use App\Core\Generator\DTO\ColumnDefinition;
use App\Core\Generator\Enums\InputType;

final class ViewFieldProcessor
{
    /**
     * @param ColumnDefinition[] $columns
     */
    public function renderForm(
        array $columns,
        string $variable
    ): string {

        $output = [];

        foreach ($columns as $column) {

            if ($this->shouldSkip($column)) {
                continue;
            }

            $output[] = $this->renderField(
                $column,
                $variable
            );
        }

        return implode(PHP_EOL . PHP_EOL, $output);
    }

    private function renderField(
        ColumnDefinition $column,
        string $variable
    ): string {

        return match ($column->type()->inputType()) {

            InputType::TEXT,
            InputType::EMAIL,
            InputType::TEL,
            InputType::URL,
            InputType::SEARCH
            => $this->renderInput($column, $variable),

            InputType::PASSWORD
            => $this->renderPassword($column, $variable),

            InputType::NUMBER,
            InputType::CURRENCY
            => $this->renderNumber($column, $variable),

            InputType::TEXTAREA,
            InputType::EDITOR
            => $this->renderTextarea($column, $variable),

            InputType::CHECKBOX,
            InputType::SWITCH
            => $this->renderCheckbox($column, $variable),

            InputType::SELECT
            => $this->renderSelect($column, $variable),

            InputType::DATE,
            InputType::TIME,
            InputType::DATETIME_LOCAL,
            InputType::MONTH,
            InputType::WEEK
            => $this->renderDate($column, $variable),

            InputType::FILE,
            InputType::IMAGE
            => $this->renderFile($column, $variable),

            default
            => $this->renderInput($column, $variable),
        };
    }

    public function renderColumns(
        array $columns,
        string $variable
    ): string {

        $fields = [];

        foreach ($columns as $column) {

            if ($this->shouldSkip($column)) {
                continue;
            }

            $fields[] = $this->renderShowField(
                $column,
                $variable
            );
        }

        return implode(PHP_EOL . PHP_EOL, $fields);
    }

     /**
     * Genera los encabezados de la tabla Index.
     *
     * @param array<ColumnDefinition> $columns
     */
    public function renderHeaders(array $columns): string
    {
        $headers = [];

        foreach ($columns as $column) {

            if (! $column->shouldAppearInTable()) {
                continue;
            }

            $headers[] = sprintf(
                '<th>%s</th>',
                ucfirst(str_replace('_', ' ', $column->name()))
            );
        }

        return implode(PHP_EOL . str_repeat(' ', 24), $headers);
    }

    /**
     * Genera las columnas del Index.
     *
     * @param array<ColumnDefinition> $columns
     */
    public function renderIndex(
        array $columns,
        string $variable
    ): string {

        $cells = [];

        foreach ($columns as $column) {

            if (! $column->shouldAppearInTable()) {
                continue;
            }

            $cells[] = sprintf(
                '<td>{{ $%s->%s }}</td>',
                $variable,
                $column->name()
            );
        }

        return implode(PHP_EOL . str_repeat(' ', 28), $cells);
    }

    private function renderShowField(
        ColumnDefinition $column,
        string $variable
    ): string {

        $label = str_replace(
            '_',
            ' ',
            ucfirst($column->name())
        );

        return <<<BLADE
<x-cn.field label="{$label}">
    {{ \${$variable}->{$column->name()} }}
</x-cn.field>
BLADE;
    }

    private function shouldSkip(
        ColumnDefinition $column
    ): bool {
        return !$column->shouldAppearInForm();
    }

    private function renderInput(
        ColumnDefinition $column,
        string $variable
    ): string {

        $label = str_replace(
            '_',
            ' ',
            ucfirst($column->name())
        );

        $name = $column->name();

        $type = $column->type()->inputType()->value;

        return <<<BLADE
<x-cn.input
    name="{$name}"
    label="{$label}"
    type="{$type}"
    :value="old('{$name}', \${$variable}->{$name} ?? '')"
/>
BLADE;
    }

    private function renderPassword(
        ColumnDefinition $column,
        string $variable
    ): string {

        $label = str_replace(
            '_',
            ' ',
            ucfirst($column->name())
        );

        $name = $column->name();

        return <<<BLADE
<x-cn.password
    name="{$name}"
    label="{$label}"
/>
BLADE;
    }

    private function renderNumber(
        ColumnDefinition $column,
        string $variable
    ): string {

        $label = str_replace(
            '_',
            ' ',
            ucfirst($column->name())
        );

        $name = $column->name();

        $type = $column->type()->inputType()->value;

        return <<<BLADE
<x-cn.input
    name="{$name}"
    label="{$label}"
    type="{$type}"
    :value="old('{$name}', \${$variable}->{$name} ?? '')"
/>
BLADE;
    }

    private function renderTextarea(
        ColumnDefinition $column,
        string $variable
    ): string {

        $label = str_replace(
            '_',
            ' ',
            ucfirst($column->name())
        );

        $name = $column->name();

        return <<<BLADE
<x-cn.textarea
    name="{$name}"
    label="{$label}"
>
{{ old('{$name}', \${$variable}->{$name} ?? '') }}
</x-cn.textarea>
BLADE;
    }

    private function renderCheckbox(
        ColumnDefinition $column,
        string $variable
    ): string {

        $label = str_replace(
            '_',
            ' ',
            ucfirst($column->name())
        );

        $name = $column->name();

        return <<<BLADE
<x-cn.checkbox
    name="{$name}"
    label="{$label}"
    :checked="old('{$name}', \${$variable}->{$name} ?? false)"
/>
BLADE;
    }

    private function renderSelect(
        ColumnDefinition $column,
        string $variable
    ): string {

        $label = str_replace(
            '_',
            ' ',
            ucfirst($column->name())
        );

        $name = $column->name();

        return <<<BLADE
<x-cn.select
    name="{$name}"
    label="{$label}"
    :options="\${$name}Options ?? []"
    :selected="old('{$name}', \${$variable}->{$name} ?? '')"
/>
BLADE;
    }
    private function renderDate(
        ColumnDefinition $column,
        string $variable
    ): string {

        $label = str_replace(
            '_',
            ' ',
            ucfirst($column->name())
        );

        $name = $column->name();

        $type = $column->type()->inputType()->value;

        return <<<BLADE
<x-cn.input
    name="{$name}"
    label="{$label}"
    type="{$type}"
    :value="old('{$name}', \${$variable}->{$name} ?? '')"
/>
BLADE;
    }

    private function renderFile(
        ColumnDefinition $column,
        string $variable
    ): string {

        $label = str_replace(
            '_',
            ' ',
            ucfirst($column->name())
        );

        $name = $column->name();

        $component = $column->type()->inputType() === InputType::IMAGE
            ? 'image'
            : 'file';

        return <<<BLADE
<x-cn.{$component}
    name="{$name}"
    label="{$label}"
/>
BLADE;
    }
}
