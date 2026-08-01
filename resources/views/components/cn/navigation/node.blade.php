@props([
    'node'
])

@php
    use App\Core\Navigation\Enums\NavigationNodeType;
@endphp

@if ($node->type() === NavigationNodeType::GROUP)

    <x-cn.navigation.group :node="$node" />

@elseif ($node->type() === NavigationNodeType::ITEM)

    <x-cn.navigation.item :node="$node" />

@endif
