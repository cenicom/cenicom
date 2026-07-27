@props([
    'node'
])

<li class="cn-navigation-item">

    <a href="{{ $node->href() }}">

        @if($node->icon())
            <i class="{{ $node->icon() }}"></i>
        @endif

        <span>
            {{ $node->label() }}
        </span>

    </a>

</li>
