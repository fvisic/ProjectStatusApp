<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('users.title') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div class="flex flex-col sm:flex-row gap-2 sm:items-center w-full">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="{{ __('users.search_placeholder') }}"
                            aria-label="{{ __('users.search_placeholder') }}"
                            class="w-full sm:max-w-xs rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        />
                        <select
                            wire:model.live="filterRole"
                            aria-label="{{ __('users.filter_role') }}"
                            class="w-full sm:w-44 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm"
                        >
                            <option value="">{{ __('users.all_roles') }}</option>
                            <option value="admin">{{ __('users.role_admin') }}</option>
                            <option value="manager">{{ __('users.role_manager') }}</option>
                            <option value="user">{{ __('users.role_user') }}</option>
                        </select>
                    </div>
                </div>

                @if ($lastResetPassword && $resettingId)
                    @php $resetUser = \App\Models\User::find($resettingId); @endphp
                    <div class="mb-4 p-4 rounded-md bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">
                                    {{ __('users.password_reset_for', ['name' => $resetUser?->name ?? '']) }}
                                </p>
                                <p class="mt-1 text-xs text-yellow-700 dark:text-yellow-300">
                                    {{ __('users.password_reset_notice') }}
                                </p>
                                <code class="mt-2 inline-block font-mono text-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-1 rounded border border-yellow-200 dark:border-yellow-800 select-all">{{ $lastResetPassword }}</code>
                            </div>
                            <button
                                wire:click="dismissPassword"
                                type="button"
                                aria-label="{{ __('users.dismiss') }}"
                                class="text-yellow-700 dark:text-yellow-300 hover:text-yellow-900 dark:hover:text-yellow-100"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('users.col_name') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('users.col_role') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('users.col_status') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">{{ __('users.col_2fa') }}</th>
                                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">{{ __('users.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($users as $user)
                                <tr wire:key="user-{{ $user->id }}" class="{{ $user->is_disabled ? 'opacity-60' : '' }}">
                                    <td class="px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                                        <div>
                                            {{ $user->name }}
                                            @if ($user->id === auth()->id())
                                                <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">({{ __('users.you') }})</span>
                                            @endif
                                        </div>
                                        @if ($user->username)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ '@' . $user->username }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-sm">
                                        <select
                                            wire:change="changeRole({{ $user->id }}, $event.target.value)"
                                            aria-label="{{ __('users.change_role_for', ['name' => $user->name]) }}"
                                            class="text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 py-1"
                                            @disabled($user->id === auth()->id())
                                        >
                                            <option value="admin" @selected($user->role === 'admin')>{{ __('users.role_admin') }}</option>
                                            <option value="manager" @selected($user->role === 'manager')>{{ __('users.role_manager') }}</option>
                                            <option value="user" @selected($user->role === 'user')>{{ __('users.role_user') }}</option>
                                        </select>
                                    </td>
                                    <td class="px-3 py-2 text-sm">
                                        @if ($user->is_disabled)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                                {{ __('users.status_disabled') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                                {{ __('users.status_active') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-sm hidden sm:table-cell">
                                        @if ($user->hasTwoFactorEnabled())
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 dark:text-green-400">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                {{ __('users.twofa_on') }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('users.twofa_off') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        <div class="flex justify-end items-center gap-1">
                                            <button
                                                wire:click="resetPassword({{ $user->id }})"
                                                wire:confirm="{{ __('users.confirm_reset_password', ['name' => $user->name]) }}"
                                                type="button"
                                                aria-label="{{ __('users.reset_password_for', ['name' => $user->name]) }}"
                                                title="{{ __('users.reset_password') }}"
                                                class="p-1.5 text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                            </button>

                                            <button
                                                wire:click="toggleDisabled({{ $user->id }})"
                                                wire:confirm="{{ $user->is_disabled ? __('users.confirm_enable', ['name' => $user->name]) : __('users.confirm_disable', ['name' => $user->name]) }}"
                                                type="button"
                                                @disabled($user->id === auth()->id())
                                                aria-label="{{ $user->is_disabled ? __('users.enable_for', ['name' => $user->name]) : __('users.disable_for', ['name' => $user->name]) }}"
                                                title="{{ $user->is_disabled ? __('users.enable') : __('users.disable') }}"
                                                class="p-1.5 text-gray-600 hover:text-red-600 dark:text-gray-300 dark:hover:text-red-400 disabled:opacity-40 disabled:cursor-not-allowed"
                                            >
                                                @if ($user->is_disabled)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                @endif
                                            </button>

                                            <button
                                                wire:click="disableTwoFactor({{ $user->id }})"
                                                wire:confirm="{{ __('users.confirm_disable_2fa', ['name' => $user->name]) }}"
                                                type="button"
                                                @disabled(! $user->two_factor_secret)
                                                aria-label="{{ __('users.disable_2fa_for', ['name' => $user->name]) }}"
                                                title="{{ __('users.disable_2fa') }}"
                                                class="p-1.5 text-gray-600 hover:text-yellow-600 dark:text-gray-300 dark:hover:text-yellow-400 disabled:opacity-40 disabled:cursor-not-allowed"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            </button>

                                            <button
                                                wire:click="resetWebauthn({{ $user->id }})"
                                                wire:confirm="{{ __('users.confirm_reset_webauthn', ['name' => $user->name]) }}"
                                                type="button"
                                                @disabled($user->webAuthnCredentials()->count() === 0)
                                                aria-label="{{ __('users.reset_webauthn_for', ['name' => $user->name]) }}"
                                                title="{{ __('users.reset_webauthn') }}"
                                                class="p-1.5 text-gray-600 hover:text-purple-600 dark:text-gray-300 dark:hover:text-purple-400 disabled:opacity-40 disabled:cursor-not-allowed"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zM12 11v2m-3-2v2m6-2v2"/></svg>
                                            </button>

                                            <button
                                                wire:click="deleteUser({{ $user->id }})"
                                                wire:confirm="{{ __('users.confirm_delete', ['name' => $user->name]) }}"
                                                type="button"
                                                @disabled($user->id === auth()->id())
                                                aria-label="{{ __('users.delete_for', ['name' => $user->name]) }}"
                                                title="{{ __('users.delete') }}"
                                                class="p-1.5 text-gray-600 hover:text-red-700 dark:text-gray-300 dark:hover:text-red-500 disabled:opacity-40 disabled:cursor-not-allowed"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('users.no_results') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
