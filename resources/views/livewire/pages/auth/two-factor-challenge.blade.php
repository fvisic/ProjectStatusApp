<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use PragmaRX\Google2FA\Google2FA;

new #[Layout('layouts.guest')] class extends Component
{
    public string $code = '';
    public string $recovery_code = '';
    public bool $useRecoveryCode = false;

    public function mount(): void
    {
        if (! Session::has('auth.2fa.user_id')) {
            $this->redirect(route('login'), navigate: true);
        }
    }

    public function toggleRecoveryCode(): void
    {
        $this->useRecoveryCode = ! $this->useRecoveryCode;
        $this->resetErrorBag();
    }

    public function submit(): void
    {
        $userId = Session::get('auth.2fa.user_id');
        $remember = (bool) Session::get('auth.2fa.remember', false);

        $user = User::find($userId);

        if (! $user) {
            Session::forget(['auth.2fa.user_id', 'auth.2fa.remember']);
            $this->redirect(route('login'), navigate: true);
            return;
        }

        if ($this->useRecoveryCode) {
            $this->validate(['recovery_code' => ['required', 'string']]);

            $codes = $user->two_factor_recovery_codes ?? [];
            $match = null;

            foreach ($codes as $code) {
                if (hash_equals($code, $this->recovery_code)) {
                    $match = $code;
                    break;
                }
            }

            if ($match === null) {
                throw ValidationException::withMessages([
                    'recovery_code' => __('security.invalid_recovery_code'),
                ]);
            }

            // Consume the recovery code.
            $user->forceFill([
                'two_factor_recovery_codes' => array_values(array_filter($codes, fn ($c) => $c !== $match)),
            ])->save();
        } else {
            $this->validate(['code' => ['required', 'string']]);

            $google2fa = new Google2FA();

            if (! $google2fa->verifyKey($user->two_factor_secret, $this->code)) {
                throw ValidationException::withMessages([
                    'code' => __('security.invalid_code'),
                ]);
            }
        }

        Auth::login($user, $remember);
        Session::forget(['auth.2fa.user_id', 'auth.2fa.remember']);
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        @if ($useRecoveryCode)
            {{ __('security.challenge_recovery_intro') }}
        @else
            {{ __('security.challenge_intro') }}
        @endif
    </div>

    <form wire:submit="submit" novalidate>
        @if ($useRecoveryCode)
            <div>
                <x-input-label for="recovery_code" :value="__('security.recovery_code')" />
                <x-text-input
                    id="recovery_code"
                    wire:model="recovery_code"
                    name="recovery_code"
                    type="text"
                    autocomplete="one-time-code"
                    class="block mt-1 w-full"
                    required
                    autofocus
                />
                <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
            </div>
        @else
            <div>
                <x-input-label for="code" :value="__('security.two_factor_code')" />
                <x-text-input
                    id="code"
                    wire:model="code"
                    name="code"
                    type="text"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    class="block mt-1 w-full"
                    required
                    autofocus
                />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>
        @endif

        <div class="flex items-center justify-between mt-4">
            <button
                type="button"
                wire:click="toggleRecoveryCode"
                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800 focus:ring-indigo-500"
            >
                @if ($useRecoveryCode)
                    {{ __('security.use_authenticator_code') }}
                @else
                    {{ __('security.use_recovery_code') }}
                @endif
            </button>

            <x-primary-button class="ms-3">
                {{ __('security.verify') }}
            </x-primary-button>
        </div>
    </form>
</div>
