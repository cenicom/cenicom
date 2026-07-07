
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.partials.head')
</head>

<body class="cds-app">

    <div class="cds-layout">

        <x-layout.sidebar />

        <div class="cds-page">

            <x-layout.topbar />

            <main class="cds-content">

                {{ $slot }}

            </main>

            <x-layout.footer />

        </div>

    </div>

    @stack('scripts')

</body>

</html>
