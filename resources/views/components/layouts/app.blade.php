<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data x-init="document.documentElement.classList.toggle('dark', localStorage.getItem('darkMode') === 'true')">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Project Status') }}</title>

        <!-- Favicon (inline SVG data URI) -->
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><rect x='4' y='2' width='16' height='20' rx='2' stroke='%23334155' stroke-width='1.5' fill='none'/><rect x='7' y='14' width='2.5' height='5' rx='0.5' fill='%2322c55e'/><rect x='10.75' y='11' width='2.5' height='8' rx='0.5' fill='%23eab308'/><rect x='14.5' y='8' width='2.5' height='11' rx='0.5' fill='%233b82f6'/></svg>" type="image/svg+xml">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased dark:bg-gray-900">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @if (session('impersonating'))
                <div class="bg-yellow-400 text-yellow-900 px-4 py-2 text-sm flex flex-wrap items-center justify-between gap-2">
                    <span>
                        👤 {{ __('impersonation.banner', ['name' => auth()->user()->name]) }}
                    </span>
                    <form method="POST" action="{{ route('impersonate.stop') }}">
                        @csrf
                        <button type="submit" class="underline font-semibold hover:text-yellow-950">
                            {{ __('impersonation.stop') }}
                        </button>
                    </form>
                </div>
            @endif

            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow dark:bg-gray-800 dark:shadow-gray-700">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 dark:text-gray-100">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            @auth
                <livewire:onboarding />
            @endauth

            <footer class="py-4 text-center text-[10px] text-gray-300 dark:text-gray-700 select-none">
                <a href="https://github.com/fvisic/ProjectStatusApp" target="_blank" rel="noopener"
                   class="hover:text-gray-400 dark:hover:text-gray-500 transition-colors">
                    Project Status
                </a>
                &middot; v{{ trim(file_get_contents(base_path('VERSION'))) }}
                &middot; &copy; {{ date('Y') }} fvisic
            </footer>
        </div>
    </body>
</html>
