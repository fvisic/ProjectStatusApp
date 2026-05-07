<?php

use Illuminate\Support\Facades\Auth;
use Laragear\WebAuthn\Models\WebAuthnCredential;
use Livewire\Volt\Component;

new class extends Component
{
    public string $newAlias = '';

    public function deleteCredential(string $id): void
    {
        $user = Auth::user();
        $credential = $user->webAuthnCredentials()->where('id', $id)->first();

        if ($credential) {
            $credential->delete();
            $this->dispatch('passkey-deleted');
        }
    }

    public function with(): array
    {
        return [
            'credentials' => Auth::user()->webAuthnCredentials()->orderByDesc('created_at')->get(),
        ];
    }
}; ?>

<section x-data="{
    alias: @entangle('newAlias').live,
    error: '',
    success: '',
    busy: false,
    supported: window.Webauthn ? window.Webauthn.webauthnSupported() : false,
    async addPasskey() {
        this.error = '';
        this.success = '';
        if (!this.alias.trim()) {
            this.error = '{{ __('security.passkey_alias_required') }}';
            return;
        }
        this.busy = true;
        try {
            await window.Webauthn.register(this.alias.trim());
            this.success = '{{ __('security.passkey_registered') }}';
            this.alias = '';
            $wire.$refresh();
        } catch (err) {
            this.error = err.message || '{{ __('security.passkey_failed') }}';
        } finally {
            this.busy = false;
        }
    }
}">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('security.passkeys_title') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('security.passkeys_description') }}
        </p>
    </header>

    <div class="mt-4">
        <template x-if="!supported">
            <div class="rounded-md bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 p-3 text-sm text-yellow-800 dark:text-yellow-200">
                {{ __('security.passkey_browser_unsupported') }}
            </div>
        </template>

        @if ($credentials->isNotEmpty())
            <ul class="divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700 rounded-md mt-4">
                @foreach ($credentials as $credential)
                    <li wire:key="cred-{{ $credential->id }}" class="flex items-center justify-between gap-3 px-3 py-2">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $credential->alias ?: __('security.passkey_unnamed') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('security.passkey_added_on', ['date' => $credential->created_at->format('d.m.Y H:i')]) }}
                                @if ($credential->disabled_at)
                                    &middot; <span class="text-red-600 dark:text-red-400">{{ __('security.passkey_disabled') }}</span>
                                @endif
                            </p>
                        </div>
                        <button
                            wire:click="deleteCredential('{{ $credential->id }}')"
                            wire:confirm="{{ __('security.confirm_delete_passkey') }}"
                            type="button"
                            aria-label="{{ __('security.delete_passkey') }}"
                            class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                        </button>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                {{ __('security.no_passkeys') }}
            </p>
        @endif

        <div class="mt-4 space-y-2">
            <label for="passkey-alias" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('security.passkey_alias_label') }}
            </label>
            <div class="flex flex-col sm:flex-row gap-2">
                <input
                    id="passkey-alias"
                    type="text"
                    x-model="alias"
                    placeholder="{{ __('security.passkey_alias_placeholder') }}"
                    maxlength="50"
                    class="flex-1 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                />
                <button
                    type="button"
                    x-on:click="addPasskey()"
                    x-bind:disabled="!supported || busy"
                    class="inline-flex items-center justify-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span x-show="!busy">{{ __('security.add_passkey') }}</span>
                    <span x-show="busy">{{ __('security.adding_passkey') }}</span>
                </button>
            </div>
            <p x-show="error" x-text="error" class="text-sm text-red-600 dark:text-red-400"></p>
            <p x-show="success" x-text="success" class="text-sm text-green-600 dark:text-green-400"></p>
        </div>
    </div>
</section>
