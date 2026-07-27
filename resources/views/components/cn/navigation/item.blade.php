@props([
    'node'
])


<li class="cn-navigation-item">

    <a href="{{ $node->route() && \Illuminate\Support\Facades\Route::has($node->route())
        ? route($node->route())
        : '#' }}">

        @if($node->icon())
            <i class="{{ $node->icon() }}"></i>
        @endif

        <span>
            {{ $node->label() }}
        </span>

    </a>

</li>
