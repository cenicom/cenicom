@props([
    'title' => '',
    'description' => null,
    'icon' => null,
])

<x-layout.app>

    <div {{ $attributes->merge(['class' => 'cn-page']) }}>

        <header class="cn-page-header">

            <div class="cn-page-title">

                @if($icon)

                    <div class="cn-page-icon">

                        <i class="fas fa-{{ $icon }}"></i>

                    </div>

                @endif

                <div>

                    <h1>{{ $title }}</h1>

                    @if($description)

                        <p>{{ $description }}</p>

                    @endif

                </div>

            </div>

            @isset($actions)

                <div class="cn-page-actions">

                    {{ $actions }}

                </div>

            @endisset

        </header>

        {{-- Aquí podremos insertar Breadcrumb en el futuro --}}

        @isset($breadcrumb)

            <nav class="cn-page-breadcrumb">

                {{ $breadcrumb }}

            </nav>

        @endisset

        <main class="cn-page-content">

            {{ $slot }}

        </main>

    </div>

</x-layout.app>
