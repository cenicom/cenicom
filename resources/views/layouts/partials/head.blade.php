<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="csrf-token" content="{{ csrf_token() }}">

<title>
    {{ $title ?? config('app.name', 'CENICOM ERP') }}
</title>

<meta name="description" content="Sistema ERP CENICOM">

<meta name="author" content="CENICOM ERP">

<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

@include('layouts.partials.styles')


@vite(['resources/css/app.css', 'resources/js/app.js'])

@stack('styles')

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
