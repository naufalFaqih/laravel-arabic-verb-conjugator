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
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('img/logo am.png') }}">
    
    {{-- Apple Touch Icon --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/logo am.png') }}">
    
    {{-- Android Chrome Icons --}}
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/logo am.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('img/logo am.png') }}">
    
    {{-- SVG Icon (modern browsers) --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">
@auth
    <meta name="auth-check" content="true">
@else
    <meta name="auth-check" content="false">
@endauth
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>{{ $title }}</title>
     @vite(['resources/css/app.css'])
     <style>
        .spinner {
          border: 4px solid rgba(0, 0, 0, 0.1);
          width: 36px;
          height: 36px;
          border-radius: 50%;
          border-left-color: #4f46e5;
          animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
        
        .text-right {
            text-align: right;
            direction: rtl;
        }
    </style>
</head>

<body class="h-full">
<!--
  <div class="min-h-screen flex flex-col">
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">Tashrif Arab</a>
                        </div>
                        
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('home') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Beranda
                            </a>
                            
                            @auth
                                <a href="{{ route('history') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Riwayat
                                </a>
                            @endauth
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        @auth
                            <div class="hidden md:flex items-center">
                                <span class="text-sm text-gray-700 mr-4">{{ Auth::user()->name }}</span>
                            </div>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 mr-4">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                Register
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav> -->
<div class="min-h-full">
  <x-navbar></x-navbar>
  <x-header>{{ $title }}</x-header>
    <main>
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
       {{ $slot }}
      </div>
    </main>
  </div>
     @auth
        @vite(['resources/js/search-history.js'])
    @endauth
</body>
</html>