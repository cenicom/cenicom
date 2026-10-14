@props(['navigation'])

<aside class="cn-sidebar" id="cn-sidebar">
    <nav aria-label="Navegación principal">
        @foreach ($navigation->nodes() as $node)
            <x-cn.navigation.node :node="$node" />
        @endforeach
    </nav>
</aside>