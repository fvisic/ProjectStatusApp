<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $authenticated = $this->form->authenticate();

        if (! $authenticated) {
            $this->redirect(route('two-factor.challenge'), navigate: true);
            return;
        }

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" novalidate>
        <!-- Email or name -->
        <div>
            <x-input-label for="login" :value="__('auth.login_label')" />
            <x-text-input wire:model="form.login" id="login" class="block mt-1 w-full" type="text" name="login" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.login')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div class="text-sm">
                @if (Route::has('register'))
                    <a class="underline text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500" href="{{ route('register') }}" wire:navigate>
                        {{ __('Register') }}
                    </a>
                @endif
            </div>
            <div class="flex items-center">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-primary-button class="ms-3">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </div>
    </form>

    <div
        x-data="{
            error: '',
            busy: false,
            supported: window.Webauthn ? window.Webauthn.webauthnSupported() : false,
            async signIn() {
                this.error = '';
                this.busy = true;
                try {
                    const email = document.getElementById('login')?.value || null;
                    await window.Webauthn.login(email);
                    window.location.href = @js(route('dashboard'));
                } catch (err) {
                    this.error = err.message || '{{ __('security.passkey_sign_in_failed') }}';
                } finally {
                    this.busy = false;
                }
            }
        }"
        x-show="supported"
        class="mt-6"
    >
        <div class="relative">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
            </div>
            <div class="relative flex justify-center text-xs">
                <span class="bg-white dark:bg-gray-800 px-2 text-gray-500 dark:text-gray-400">
                    {{ __('Or') }}
                </span>
            </div>
        </div>

        <button
            type="button"
            x-on:click="signIn()"
            x-bind:disabled="busy"
            class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            {{ __('security.sign_in_with_passkey') }}
        </button>
        <p x-show="error" x-text="error" class="mt-2 text-sm text-red-600 dark:text-red-400"></p>
    </div>
</div>
