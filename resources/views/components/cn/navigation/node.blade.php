@props([
    'node'
])

@switch($node->type())

    @case('GROUP')

        <x-cn.navigation.group
            :node="$node"
        />

        @break

    @case('ITEM')

        <x-cn.navigation.item
            :node="$node"
        />

        @break

@endswitch
