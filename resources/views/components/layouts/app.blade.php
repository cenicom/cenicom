<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        {{ $title ?? config('app.name') }}
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>

    <div class="cn-app">

        <x-layouts.topbar />

        <div class="cn-shell">

            <x-cn.navigation.sidebar :navigation="$navigation" />

            <main class="cn-main">
                {{ $slot }}
            </main>

        </div>

    </div>
</body>

</html>
