@props([
    'navigation'
])


<aside class="cn-sidebar">

    <nav>

        @foreach($navigation->nodes() as $node)

            <x-cn.navigation.node
                :node="$node"
            />

        @endforeach

    </nav>

</aside>
