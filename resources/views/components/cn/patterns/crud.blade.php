@props([
    'title' => null,
    'subtitle' => null,
])

<div class="cn-crud">

    @if ($title || $subtitle)

        <div class="cn-crud-header">

            <div>

                @if($title)
                    <h2>{{ $title }}</h2>
                @endif

                @if($subtitle)
                    <p>{{ $subtitle }}</p>
                @endif

            </div>

        </div>

    @endif

    @isset($actions)

        <div class="cn-crud-actions">

            {{ $actions }}

        </div>

    @endisset

    @isset($filters)

        <div class="cn-crud-filters">

            {{ $filters }}

        </div>

    @endisset

    @isset($toolbar)

        <div class="cn-crud-toolbar">

            {{ $toolbar }}

        </div>

    @endisset

    <div class="cn-crud-content">

        {{ $slot }}

    </div>

    @isset($pagination)

        <div class="cn-crud-pagination">

            {{ $pagination }}

        </div>

    @endisset

    @isset($footer)

        <div class="cn-crud-footer">

            {{ $footer }}

        </div>

    @endisset

</div>
