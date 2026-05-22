<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon Configuration --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo am.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/logo am.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo am.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/logo am.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/logo am.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('img/logo am.png') }}">

    @auth
        <meta name="auth-check" content="true">
    @else
        <meta name="auth-check" content="false">
    @endauth

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Arabic display font (used by tashrif tables & keyboard) --}}
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    {{-- UI fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <title>{{ $title ?? config('app.name', 'Tashrif Arabic Verbs') }}</title>
</head>

<body class="h-full">
    <div class="min-h-full">
        <x-navbar></x-navbar>

        <main>
            <div class="mx-auto max-w-full px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- On-screen Arabic keyboard (auto-shown for inputs.arabic-input) --}}
    <x-arabic-keyboard />

    @livewireScripts
    @stack('scripts')
</body>
</html>
