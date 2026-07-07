<?php

namespace App\Support\Actions;

class Action
{
    protected string $name;

    protected string $label = '';

    protected string $icon = '';

    protected string $color = 'primary';

    protected ?string $route = null;

    protected ?string $permission = null;

    protected bool $confirm = false;

    public static function make(string $name): static
    {
        $instance = new static();
        $instance->name = $name;

        return $instance;
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function color(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function route(?string $route): static
    {
        $this->route = $route;
        return $this;
    }

    public function permission(?string $permission): static
    {
        $this->permission = $permission;
        return $this;
    }

    public function confirm(bool $confirm = true): static
    {
        $this->confirm = $confirm;
        return $this;
    }
}
