@props([
    'navigation'
])


<aside class="cn-sidebar">

     <pre>
        {{ print_r($navigation->nodes(), true) }}
    </pre>

    <nav>

        @foreach($navigation->nodes() as $node)

            <x-cn.navigation.node
                :node="$node"
            />

        @endforeach

    </nav>

</aside>
