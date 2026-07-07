<x-ui.card
    :title="$title"
    icon="filter">

    {{ $slot }}

    @isset($actions)

        <div class="mt-3 text-end">

            {{ $actions }}

        </div>

    @endisset

</x-ui.card>
