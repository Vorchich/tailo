<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    {{-- Tailwind і Filament CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Додатково можна підключити Filament CSS без сайдбару --}}
    <link rel="stylesheet" href="{{ asset('vendor/filament/filament.css') }}">
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen">
        <main class="p-6">
            @yield('content')
        </main>
    </div>

    {{-- 1. Livewire Scripts --}}
    @livewireScripts

    {{-- 2. Filament JS (Повинен бути після Livewire, оскільки залежить від Alpine) --}}
    <script src="{{ asset('vendor/filament/filament.js') }}"></script>

    {{-- 3. Flowforge JS (Можливо, він вже включений у filament.js, але якщо ні, додайте його): --}}
    <script src="{{ asset('vendor/flowforge/flowforge.js') }}"></script>
    {{-- Якщо назва файлу інша, вам потрібно знайти її в public/vendor/flowforge --}}
</body>
</html>
