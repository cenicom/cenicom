
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
        <main class="cn-main">
            {{ $slot }}
        </main>
    </div>
    <x-layout.footer />
</div>
@stack('scripts')
</body>
</html>
