<div>
@if ($show)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:key="onboarding">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full mx-4 overflow-hidden">
        {{-- Progress bar --}}
        <div class="h-1.5 bg-gray-100 dark:bg-gray-700">
            <div class="h-1.5 bg-blue-600 transition-all duration-300" style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
        </div>

        <div class="p-8">
            {{-- Step content --}}
            @if ($step === 1)
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ __('onboarding.welcome_title') }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('onboarding.welcome_desc') }}</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4 text-sm text-blue-800 dark:text-blue-400">
                    <p>{{ __('onboarding.welcome_hint') }}</p>
                </div>
            @elseif ($step === 2)
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ __('onboarding.create_title') }}</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('onboarding.create_desc') }}</p>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">1</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('onboarding.create_step1') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('onboarding.create_step1_hint') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">2</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('onboarding.create_step2') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('onboarding.create_step2_hint') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">3</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('onboarding.create_step3') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('onboarding.create_step3_hint') }}</p>
                        </div>
                    </div>
                </div>
            @elseif ($step === 3)
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ __('onboarding.views_title') }}</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('onboarding.views_desc') }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <div class="text-2xl mb-2">
                            <svg class="w-8 h-8 mx-auto text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('projects.view_list') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('onboarding.view_list_desc') }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <div class="text-2xl mb-2">
                            <svg class="w-8 h-8 mx-auto text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('projects.view_kanban') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('onboarding.view_kanban_desc') }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <div class="text-2xl mb-2">
                            <svg class="w-8 h-8 mx-auto text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12M8 12h8m-8 5h4M3 7h.01M3 12h.01M3 17h.01"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('projects.view_timeline') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('onboarding.view_timeline_desc') }}</p>
                    </div>
                </div>
            @elseif ($step === 4)
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ __('onboarding.dashboard_title') }}</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('onboarding.dashboard_desc') }}</p>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 bg-green-50 dark:bg-green-900/30 rounded-lg p-3">
                        <span class="text-lg">📊</span>
                        <p class="text-sm text-gray-700 dark:text-gray-200">{{ __('onboarding.dashboard_kpi') }}</p>
                    </div>
                    <div class="flex items-center gap-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg p-3">
                        <span class="text-lg">📈</span>
                        <p class="text-sm text-gray-700 dark:text-gray-200">{{ __('onboarding.dashboard_trends') }}</p>
                    </div>
                    <div class="flex items-center gap-3 bg-red-50 dark:bg-red-900/30 rounded-lg p-3">
                        <span class="text-lg">🚨</span>
                        <p class="text-sm text-gray-700 dark:text-gray-200">{{ __('onboarding.dashboard_alerts') }}</p>
                    </div>
                </div>
            @elseif ($step === 5)
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ __('onboarding.export_title') }}</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('onboarding.export_desc') }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="font-semibold text-sm text-gray-900 dark:text-gray-100 mb-1">📄 PDF</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('onboarding.export_pdf') }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="font-semibold text-sm text-gray-900 dark:text-gray-100 mb-1">📊 Excel / CSV</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('onboarding.export_excel') }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="font-semibold text-sm text-gray-900 dark:text-gray-100 mb-1">📋 {{ __('onboarding.export_portfolio') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('onboarding.export_portfolio_desc') }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="font-semibold text-sm text-gray-900 dark:text-gray-100 mb-1">🕓 {{ __('onboarding.export_history') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('onboarding.export_history_desc') }}</p>
                    </div>
                </div>
            @elseif ($step === 6)
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ __('onboarding.notifications_title') }}</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('onboarding.notifications_desc') }}</p>
                </div>
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">📧 {{ __('onboarding.notif_email') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('onboarding.notif_email_desc') }}</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">💬 {{ __('onboarding.notif_webhook') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('onboarding.notif_webhook_desc') }}</p>
                    </div>
                    <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-lg p-3 border border-yellow-200 dark:border-yellow-700">
                        <p class="text-xs text-yellow-800 dark:text-yellow-400">{{ __('onboarding.notif_setup_hint') }}</p>
                    </div>
                </div>
            @elseif ($step === 7)
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ __('onboarding.done_title') }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('onboarding.done_desc') }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                    <p>{{ __('onboarding.done_hint1') }}</p>
                    <p>{{ __('onboarding.done_hint2') }}</p>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="px-8 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="text-xs text-gray-400 dark:text-gray-500">
                {{ $step }} / {{ $totalSteps }}
            </div>
            <div class="flex items-center gap-2">
                @if ($step === 1)
                    <button wire:click="skip" class="px-3 py-1.5 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                        {{ __('onboarding.skip') }}
                    </button>
                @endif
                @if ($step > 1)
                    <button wire:click="prevStep" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition">
                        {{ __('onboarding.prev') }}
                    </button>
                @endif
                @if ($step < $totalSteps)
                    <button wire:click="nextStep" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        {{ __('onboarding.next') }}
                    </button>
                @else
                    <button wire:click="complete" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        {{ __('onboarding.finish') }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
</div>
