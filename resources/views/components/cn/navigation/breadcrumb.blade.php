@props([
    'items' => [],
])

@if ($items !== [])
    <nav
        class="cn-breadcrumb"
        aria-label="breadcrumb"
    >
        <ol class="cn-breadcrumb__list">

            @foreach ($items as $item)
                <li
                    class="cn-breadcrumb__item{{ ($item['current'] ?? false) ? ' cn-breadcrumb__item--current' : '' }}"
                    @if ($item['current'] ?? false) aria-current="page" @endif
                >
                    @if (($item['url'] ?? null) !== null && ! ($item['current'] ?? false))
                        <a
                            href="{{ $item['url'] }}"
                            class="cn-breadcrumb__link"
                        >
                            {{ $item['label'] }}
                        </a>
                    @else
                        {{ $item['label'] }}
                    @endif
                </li>
            @endforeach

        </ol>
    </nav>
@endif
