<?php

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Volt\Component;
use PragmaRX\Google2FA\Google2FA;

new class extends Component
{
    public bool $showingQrCode = false;
    public bool $showingRecoveryCodes = false;
    public bool $showingConfirmation = false;
    public string $code = '';

    public function mount(): void
    {
        $this->showingRecoveryCodes = false;
    }

    public function enable(): void
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        $user->forceFill([
            'two_factor_secret' => $google2fa->generateSecretKey(),
            'two_factor_recovery_codes' => collect(range(1, 8))
                ->map(fn () => Str::random(10) . '-' . Str::random(10))
                ->all(),
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->showingQrCode = true;
        $this->showingConfirmation = true;
    }

    public function confirm(): void
    {
        $this->validate([
            'code' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA();

        if (! $google2fa->verifyKey($user->two_factor_secret, $this->code)) {
            $this->addError('code', __('security.invalid_code'));
            return;
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->showingQrCode = false;
        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = true;
        $this->code = '';

        $this->dispatch('two-factor-enabled');
    }

    public function disable(): void
    {
        Auth::user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->showingQrCode = false;
        $this->showingRecoveryCodes = false;
        $this->showingConfirmation = false;
        $this->code = '';

        $this->dispatch('two-factor-disabled');
    }

    public function regenerateRecoveryCodes(): void
    {
        Auth::user()->forceFill([
            'two_factor_recovery_codes' => collect(range(1, 8))
                ->map(fn () => Str::random(10) . '-' . Str::random(10))
                ->all(),
        ])->save();

        $this->showingRecoveryCodes = true;
    }

    public function toggleRecoveryCodes(): void
    {
        $this->showingRecoveryCodes = ! $this->showingRecoveryCodes;
    }

    public function getQrCodeSvgProperty(): string
    {
        $user = Auth::user();
        if (! $user->two_factor_secret) {
            return '';
        }

        $google2fa = new Google2FA();
        $url = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_factor_secret,
        );

        $renderer = new ImageRenderer(
            new RendererStyle(192, 0),
            new SvgImageBackEnd()
        );

        return (new Writer($renderer))->writeString($url);
    }

    public function getRecoveryCodesProperty(): array
    {
        return Auth::user()->two_factor_recovery_codes ?? [];
    }
}; ?>

<section x-data="{ showingRecoveryCodes: @entangle('showingRecoveryCodes').live }">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('security.two_factor_title') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('security.two_factor_description') }}
        </p>
    </header>

    @if (auth()->user()->hasTwoFactorEnabled())
        <h3 class="mt-6 text-sm font-semibold text-green-700 dark:text-green-400 flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ __('security.two_factor_enabled') }}
        </h3>
    @elseif (auth()->user()->two_factor_secret)
        <h3 class="mt-6 text-sm font-semibold text-yellow-700 dark:text-yellow-400">
            {{ __('security.two_factor_not_confirmed') }}
        </h3>
    @else
        <h3 class="mt-6 text-sm font-semibold text-gray-900 dark:text-gray-100">
            {{ __('security.two_factor_disabled') }}
        </h3>
    @endif

    @if ($showingConfirmation && auth()->user()->two_factor_secret && ! auth()->user()->hasTwoFactorEnabled())
        <div class="mt-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('security.two_factor_scan_qr') }}
            </p>

            <div class="mt-4 inline-block p-4 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600">
                {!! $this->qrCodeSvg !!}
            </div>

            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                {{ __('security.two_factor_setup_key') }}:
                <code class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ auth()->user()->two_factor_secret }}</code>
            </p>

            <form wire:submit="confirm" class="mt-4" novalidate>
                <x-input-label for="two_factor_code" :value="__('security.two_factor_code')" />
                <x-text-input
                    id="two_factor_code"
                    wire:model="code"
                    type="text"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    class="mt-1 block w-full sm:w-48"
                    autofocus
                />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />

                <div class="mt-4 flex gap-3">
                    <x-primary-button>{{ __('security.confirm') }}</x-primary-button>
                    <x-secondary-button wire:click="disable" type="button">
                        {{ __('security.cancel') }}
                    </x-secondary-button>
                </div>
            </form>
        </div>
    @endif

    @if ($showingRecoveryCodes && auth()->user()->two_factor_recovery_codes)
        <div class="mt-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('security.two_factor_recovery_intro') }}
            </p>

            <div class="mt-4 grid gap-1 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-gray-900 rounded-lg">
                @foreach ($this->recoveryCodes as $recoveryCode)
                    <div class="text-gray-900 dark:text-gray-100">{{ $recoveryCode }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-6 flex flex-wrap items-center gap-3">
        @if (! auth()->user()->two_factor_secret)
            <x-primary-button wire:click="enable">
                {{ __('security.enable') }}
            </x-primary-button>
        @else
            @if (auth()->user()->hasTwoFactorEnabled())
                <x-secondary-button wire:click="regenerateRecoveryCodes" type="button">
                    {{ __('security.regenerate_recovery_codes') }}
                </x-secondary-button>

                <x-secondary-button wire:click="toggleRecoveryCodes" type="button">
                    {{ $showingRecoveryCodes ? __('security.hide_recovery_codes') : __('security.show_recovery_codes') }}
                </x-secondary-button>
            @endif

            <button
                wire:click="disable"
                type="button"
                class="inline-flex items-center px-4 py-2 bg-red-600 dark:bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 focus:ring-offset-gray-800 transition ease-in-out duration-150"
                wire:confirm="{{ __('security.confirm_disable') }}"
            >
                {{ __('security.disable') }}
            </button>
        @endif

        <x-action-message class="me-3" on="two-factor-enabled">
            {{ __('security.two_factor_enabled_message') }}
        </x-action-message>
        <x-action-message class="me-3" on="two-factor-disabled">
            {{ __('security.two_factor_disabled_message') }}
        </x-action-message>
    </div>
</section>
