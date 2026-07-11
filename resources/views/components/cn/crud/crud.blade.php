@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'fluid' => false,
])

<div {{ $attributes->class(['cn-crud', 'container-fluid' => $fluid, 'container' => !$fluid]) }}>

    <x-cn.card>

        {{-- Header del CRUD --}}
        <x-cn.crud.header :title="$title" :subtitle="$subtitle" :icon="$icon" />


        {{-- Barra de herramientas --}}
        @isset($toolbar)
            {{ $toolbar }}
        @endisset


        {{-- Zona de filtros --}}
        @isset($filters)
            {{ $filters }}
        @endisset


        {{-- Acciones del CRUD --}}
        @isset($actions)
            {{ $actions }}
        @endisset


        {{-- Contenido principal --}}
        <div class="cn-crud__content">

            @isset($empty)
                {{ $empty }}
            @else
                {{ $slot }}
            @endisset

        </div>


        {{-- Pie del CRUD --}}
        @isset($footer)
            <div class="cn-crud__footer">
                {{ $footer }}
            </div>
        @endisset


    </x-cn.card>

</div>
