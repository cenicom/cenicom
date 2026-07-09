<?php
/**
 * CN UI Framework
 *
 * Component: x-cn-col
 * Level: Structural
 * Version: 1.0.0
 *
 * Represents a responsive grid column.
 */

$classes = [
    'col',
    $span ? "col-{$span}" : null,
    $sm ? "col-sm-{$sm}" : null,
    $md ? "col-md-{$md}" : null,
    $lg ? "col-lg-{$lg}" : null,
    $xl ? "col-xl-{$xl}" : null,
    $xxl ? "col-xxl-{$xxl}" : null,
];
