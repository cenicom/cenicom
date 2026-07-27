@props([
    'node'
])


<li class="cn-navigation-group">

    <div class="cn-navigation-group-header">

        @if($node->icon())

            <i class="{{ $node->icon() }}"></i>

        @endif


        <span>
            {{ $node->label() }}
        </span>

    </div>


    @if($node->hasChildren())

        <ul class="cn-navigation-children">

            @foreach($node->children() as $child)

                <x-cn.navigation.node
                    :node="$child"
                />

            @endforeach

        </ul>

    @endif

</li>
