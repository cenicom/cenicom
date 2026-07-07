<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.head')
</head>

<body>
    <div class="cn-app">
        <x-layout.topbar />
        <div class="cn-content">
            <x-layout.sidebar />
            @if (session('success'))
                <x-cn.alert type="success" title="Operación exitosa">
                    {{ session('success') }}
                </x-cn.alert>
            @endif

            @if (session('error'))
                <x-cn.alert type="danger" title="Error">
                    {{ session('error') }}
                </x-cn.alert>
            @endif

            @if (session('warning'))
                <x-cn.alert type="warning" title="Advertencia">
                    {{ session('warning') }}
                </x-cn.alert>
            @endif

            @if (session('info'))
                <x-cn.alert type="info" title="Información">
                    {{ session('info') }}
                </x-cn.alert>
            @endif

            {{ $slot }}
            <main class="cn-main">
                {{ $slot }}
            </main>
        </div>
        <x-layout.footer />
    </div>
    @stack('scripts')
</body>

</html>
