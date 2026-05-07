<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 dark:bg-gray-800 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate aria-label="{{ __('dashboard.aria_home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('dashboard.nav_dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.index')" wire:navigate>
                        {{ __('projects.view_list') }}
                    </x-nav-link>
                    <x-nav-link :href="route('projects.kanban')" :active="request()->routeIs('projects.kanban')" wire:navigate>
                        {{ __('projects.view_kanban') }}
                    </x-nav-link>
                    <x-nav-link :href="route('projects.timeline')" :active="request()->routeIs('projects.timeline')" wire:navigate>
                        {{ __('projects.view_timeline') }}
                    </x-nav-link>
                    <x-nav-link :href="route('docs')" :active="request()->routeIs('docs')" wire:navigate>
                        {{ __('docs.nav') }}
                    </x-nav-link>
                    @if (auth()->user()->isAdmin())
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')" wire:navigate>
                            {{ __('users.nav') }}
                        </x-nav-link>
                        <x-nav-link :href="route('project-types.index')" :active="request()->routeIs('project-types.index')" wire:navigate>
                            {{ __('project_types.nav') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Dark Mode Toggle + Locale Switcher + Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 sm:gap-3">
                <livewire:dark-mode-toggle />
                <livewire:locale-switcher />
                <livewire:notification-center />
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @if (auth()->user()->isAdmin() && ! session('impersonating'))
                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                            <div class="px-4 py-2 text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                {{ __('impersonation.pick_user') }}
                            </div>
                            @foreach (\App\Models\User::query()->where('id', '!=', auth()->id())->whereNotIn('role', ['admin'])->where('is_admin', false)->orderBy('name')->get() as $impersonableUser)
                                <form method="POST" action="{{ route('impersonate.start', $impersonableUser) }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        {{ $impersonableUser->name }}
                                    </button>
                                </form>
                            @endforeach
                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                        @endif

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" type="button"
                        :aria-label="open ? '{{ __('dashboard.aria_close_menu') }}' : '{{ __('dashboard.aria_open_menu') }}'"
                        :aria-expanded="open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:focus:bg-gray-700">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('dashboard.nav_dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.index')" wire:navigate>
                {{ __('projects.view_list') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('projects.kanban')" :active="request()->routeIs('projects.kanban')" wire:navigate>
                {{ __('projects.view_kanban') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('projects.timeline')" :active="request()->routeIs('projects.timeline')" wire:navigate>
                {{ __('projects.view_timeline') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('docs')" :active="request()->routeIs('docs')" wire:navigate>
                {{ __('docs.nav') }}
            </x-responsive-nav-link>
            @if (auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')" wire:navigate>
                    {{ __('users.nav') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('project-types.index')" :active="request()->routeIs('project-types.index')" wire:navigate>
                    {{ __('project_types.nav') }}
                </x-responsive-nav-link>
            @endif
            <div class="px-4 py-2 flex items-center gap-3">
                <livewire:dark-mode-toggle />
                <livewire:locale-switcher />
                <livewire:notification-center />
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
