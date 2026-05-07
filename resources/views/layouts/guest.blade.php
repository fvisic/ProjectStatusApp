<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data x-init="document.documentElement.classList.toggle('dark', localStorage.getItem('darkMode') === 'true')">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Project Status') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><rect x='4' y='2' width='16' height='20' rx='2' stroke='%23334155' stroke-width='1.5' fill='none'/><rect x='7' y='14' width='2.5' height='5' rx='0.5' fill='%2322c55e'/><rect x='10.75' y='11' width='2.5' height='8' rx='0.5' fill='%23eab308'/><rect x='14.5' y='8' width='2.5' height='11' rx='0.5' fill='%233b82f6'/></svg>" type="image/svg+xml">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div class="absolute top-4 right-4 flex items-center gap-3">
                <livewire:locale-switcher />
                <livewire:dark-mode-toggle />
            </div>
            <div>
                <a href="/" wire:navigate aria-label="{{ __('dashboard.aria_home') }}">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg dark:bg-gray-800">
                {{ $slot }}
            </div>

            <footer class="mt-8 text-center text-[10px] text-gray-300 dark:text-gray-700 select-none">
                &copy; {{ date('Y') }} fvisic &middot; All rights reserved &middot; Powered by fvisic
            </footer>
        </div>
    </body>
</html>
