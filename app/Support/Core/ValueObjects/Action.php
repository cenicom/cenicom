<?php
final class Action
{
    public function __construct(

        public readonly string $label,

        public readonly string $icon,

        public readonly string $variant,

        public readonly string $type,

    ){}
}
