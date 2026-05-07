<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('docs.title') }}</h2>
            <livewire:onboarding-trigger />
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mobile section navigation --}}
            <div class="md:hidden mb-4">
                <select wire:change="$set('section', $event.target.value)" aria-label="{{ __('projects.aria_section_nav') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach ($sections as $key => $label)
                        <option value="{{ $key }}" {{ $section === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-6">
                {{-- Sidebar --}}
                <nav class="w-56 flex-shrink-0 hidden md:block">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3 sticky top-8">
                        @foreach ($sections as $key => $label)
                            <button wire:click="$set('section', '{{ $key }}')"
                                    class="block w-full text-left px-3 py-2 text-sm rounded-md transition {{ $section === $key ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </nav>

                {{-- Content --}}
                <div class="flex-1 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8 prose prose-sm max-w-none dark:prose-invert">
                    @if ($section === 'overview')
                        <h2>{{ __('docs.overview_title') }}</h2>
                        <p>{{ __('docs.overview_intro') }}</p>

                        <div class="not-prose grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 my-6">
                            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4 text-center">
                                <div class="text-3xl mb-2">📋</div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('docs.feature_tracking') }}</p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/30 rounded-lg p-4 text-center">
                                <div class="text-3xl mb-2">📊</div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('docs.feature_dashboard') }}</p>
                            </div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-lg p-4 text-center">
                                <div class="text-3xl mb-2">📈</div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('docs.feature_reports') }}</p>
                            </div>
                            <div class="bg-purple-50 dark:bg-purple-900/30 rounded-lg p-4 text-center">
                                <div class="text-3xl mb-2">🔔</div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('docs.feature_notifications') }}</p>
                            </div>
                            <div class="bg-red-50 dark:bg-red-900/30 rounded-lg p-4 text-center">
                                <div class="text-3xl mb-2">🕓</div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('docs.feature_history') }}</p>
                            </div>
                            <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-lg p-4 text-center">
                                <div class="text-3xl mb-2">🌐</div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('docs.feature_i18n') }}</p>
                            </div>
                        </div>

                        <h3>{{ __('docs.overview_roles_title') }}</h3>
                        <div class="not-prose">
                            <table class="min-w-full text-sm">
                                <thead><tr class="bg-gray-50 dark:bg-gray-700"><th class="px-4 py-2 text-left font-semibold">{{ __('docs.role') }}</th><th class="px-4 py-2 text-left font-semibold">{{ __('docs.role_permissions') }}</th></tr></thead>
                                <tbody>
                                    <tr class="border-t"><td class="px-4 py-2 font-medium">{{ __('docs.role_user') }}</td><td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ __('docs.role_user_desc') }}</td></tr>
                                    <tr class="border-t"><td class="px-4 py-2 font-medium">{{ __('docs.role_admin') }}</td><td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ __('docs.role_admin_desc') }}</td></tr>
                                </tbody>
                            </table>
                        </div>

                    @elseif ($section === 'projects')
                        <h2>{{ __('docs.projects_title') }}</h2>
                        <p>{{ __('docs.projects_intro') }}</p>

                        <h3>{{ __('docs.projects_create_title') }}</h3>
                        <div class="not-prose bg-gray-50 dark:bg-gray-700 rounded-lg p-4 my-4">
                            <ol class="space-y-3 text-sm">
                                <li class="flex gap-3"><span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">1</span><span>{{ __('docs.projects_create_s1') }}</span></li>
                                <li class="flex gap-3"><span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">2</span><span>{{ __('docs.projects_create_s2') }}</span></li>
                                <li class="flex gap-3"><span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">3</span><span>{{ __('docs.projects_create_s3') }}</span></li>
                                <li class="flex gap-3"><span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">4</span><span>{{ __('docs.projects_create_s4') }}</span></li>
                            </ol>
                        </div>

                        <h3>{{ __('docs.projects_tabs_title') }}</h3>
                        <div class="not-prose space-y-2 text-sm">
                            <div class="bg-blue-50 dark:bg-blue-900/30 rounded p-3"><strong>{{ __('projects.tab_basic') }}:</strong> {{ __('docs.tab_basic_desc') }}</div>
                            <div class="bg-green-50 dark:bg-green-900/30 rounded p-3"><strong>{{ __('projects.tab_phases') }}:</strong> {{ __('docs.tab_phases_desc') }}</div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded p-3"><strong>{{ __('projects.tab_estimation') }}:</strong> {{ __('docs.tab_estimation_desc') }}</div>
                            <div class="bg-red-50 dark:bg-red-900/30 rounded p-3"><strong>{{ __('projects.tab_risks') }}:</strong> {{ __('docs.tab_risks_desc') }}</div>
                            <div class="bg-purple-50 dark:bg-purple-900/30 rounded p-3"><strong>{{ __('projects.tab_burndown') }}:</strong> {{ __('docs.tab_burndown_desc') }}</div>
                        </div>

                        <h3>{{ __('docs.projects_health_title') }}</h3>
                        <div class="not-prose space-y-2 text-sm">
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-400"></span><strong>{{ __('projects.health_on_track') }}:</strong> {{ __('docs.health_on_track_desc') }}</div>
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-yellow-400"></span><strong>{{ __('projects.health_at_risk') }}:</strong> {{ __('docs.health_at_risk_desc') }}</div>
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-400"></span><strong>{{ __('projects.health_off_track') }}:</strong> {{ __('docs.health_off_track_desc') }}</div>
                        </div>

                        <h3>{{ __('docs.projects_phases_title') }}</h3>
                        <p>{{ __('docs.projects_phases_intro') }}</p>
                        <div class="not-prose">
                            <div class="flex items-center gap-1 text-xs flex-wrap my-3">
                                @foreach (['instalacija_analiza', 'funkcionalna_specifikacija', 'implementacija_testiranje', 'integracije', 'uat_edukacija', 'go_live', 'hypercare'] as $i => $phase)
                                    @if ($i > 0) <span class="text-gray-300 dark:text-gray-600">→</span> @endif
                                    <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ __('projects.phases.' . $phase) }}</span>
                                @endforeach
                            </div>
                        </div>

                    @elseif ($section === 'views')
                        <h2>{{ __('docs.views_title') }}</h2>

                        <h3>{{ __('docs.view_list_title') }}</h3>
                        <p>{{ __('docs.view_list_full_desc') }}</p>
                        <div class="not-prose bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3 my-3 text-sm">
                            <strong>{{ __('docs.tip') }}:</strong> {{ __('docs.view_list_tip') }}
                        </div>

                        <h3>{{ __('docs.view_kanban_title') }}</h3>
                        <p>{{ __('docs.view_kanban_full_desc') }}</p>
                        <div class="not-prose bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-3 my-3 text-sm">
                            <strong>{{ __('docs.tip') }}:</strong> {{ __('docs.view_kanban_tip') }}
                        </div>

                        <h3>{{ __('docs.view_timeline_title') }}</h3>
                        <p>{{ __('docs.view_timeline_full_desc') }}</p>
                        <div class="not-prose bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-3 my-3 text-sm">
                            <strong>{{ __('docs.tip') }}:</strong> {{ __('docs.view_timeline_tip') }}
                        </div>

                    @elseif ($section === 'dashboard')
                        <h2>{{ __('docs.dashboard_title') }}</h2>
                        <p>{{ __('docs.dashboard_intro') }}</p>

                        <h3>{{ __('docs.dashboard_kpi_title') }}</h3>
                        <p>{{ __('docs.dashboard_kpi_desc') }}</p>

                        <h3>{{ __('docs.dashboard_trends_title') }}</h3>
                        <p>{{ __('docs.dashboard_trends_desc') }}</p>

                        <h3>{{ __('docs.dashboard_alerts_title') }}</h3>
                        <div class="not-prose space-y-2 text-sm my-3">
                            <div class="bg-red-50 dark:bg-red-900/30 rounded p-3">🔴 {{ __('docs.alert_offtrack') }}</div>
                            <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded p-3">📅 {{ __('docs.alert_golive') }}</div>
                            <div class="bg-orange-50 dark:bg-orange-900/30 rounded p-3">💰 {{ __('docs.alert_budget') }}</div>
                        </div>

                    @elseif ($section === 'exports')
                        <h2>{{ __('docs.exports_title') }}</h2>
                        <p>{{ __('docs.exports_intro') }}</p>

                        <h3>{{ __('docs.export_single_pdf_title') }}</h3>
                        <p>{{ __('docs.export_single_pdf_desc') }}</p>

                        <h3>{{ __('docs.export_portfolio_title') }}</h3>
                        <p>{{ __('docs.export_portfolio_desc') }}</p>

                        <h3>{{ __('docs.export_excel_title') }}</h3>
                        <p>{{ __('docs.export_excel_desc') }}</p>

                    @elseif ($section === 'notifications')
                        <h2>{{ __('docs.notifications_title') }}</h2>
                        <p>{{ __('docs.notifications_intro') }}</p>

                        <h3>{{ __('docs.notif_daily_title') }}</h3>
                        <p>{{ __('docs.notif_daily_desc') }}</p>

                        <h3>{{ __('docs.notif_weekly_title') }}</h3>
                        <p>{{ __('docs.notif_weekly_desc') }}</p>

                        <h3>{{ __('docs.notif_webhook_title') }}</h3>
                        <p>{{ __('docs.notif_webhook_desc') }}</p>
                        <div class="not-prose bg-gray-50 dark:bg-gray-700 rounded-lg p-4 my-3 text-sm">
                            <p class="font-semibold mb-2">{{ __('docs.notif_webhook_setup') }}</p>
                            <ol class="list-decimal list-inside space-y-1 text-gray-600 dark:text-gray-400">
                                <li>{{ __('docs.notif_webhook_s1') }}</li>
                                <li>{{ __('docs.notif_webhook_s2') }}</li>
                                <li>{{ __('docs.notif_webhook_s3') }}</li>
                            </ol>
                        </div>

                    @elseif ($section === 'security')
                        <h2>{{ __('docs.security_title') }}</h2>
                        <p>{{ __('docs.security_intro') }}</p>

                        <h3>{{ __('docs.security_roles_title') }}</h3>
                        <p>{{ __('docs.security_roles_intro') }}</p>
                        <div class="not-prose">
                            <table class="min-w-full text-sm">
                                <thead><tr class="bg-gray-50 dark:bg-gray-700"><th class="px-4 py-2 text-left font-semibold">{{ __('docs.role') }}</th><th class="px-4 py-2 text-left font-semibold">{{ __('docs.role_permissions') }}</th></tr></thead>
                                <tbody>
                                    <tr class="border-t"><td class="px-4 py-2 font-medium">{{ __('users.role_admin') }}</td><td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ __('docs.security_role_admin_desc') }}</td></tr>
                                    <tr class="border-t"><td class="px-4 py-2 font-medium">{{ __('users.role_manager') }}</td><td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ __('docs.security_role_manager_desc') }}</td></tr>
                                    <tr class="border-t"><td class="px-4 py-2 font-medium">{{ __('users.role_user') }}</td><td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ __('docs.security_role_user_desc') }}</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <h3>{{ __('docs.security_2fa_title') }}</h3>
                        <p>{{ __('docs.security_2fa_intro') }}</p>
                        <div class="not-prose bg-gray-50 dark:bg-gray-700 rounded-lg p-4 my-4">
                            <ol class="space-y-2 text-sm">
                                <li class="flex gap-3"><span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">1</span><span>{{ __('docs.security_2fa_s1') }}</span></li>
                                <li class="flex gap-3"><span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">2</span><span>{{ __('docs.security_2fa_s2') }}</span></li>
                                <li class="flex gap-3"><span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">3</span><span>{{ __('docs.security_2fa_s3') }}</span></li>
                                <li class="flex gap-3"><span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">4</span><span>{{ __('docs.security_2fa_s4') }}</span></li>
                            </ol>
                        </div>
                        <div class="not-prose bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 p-3 my-3 text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>{{ __('docs.tip') }}:</strong> {{ __('docs.security_2fa_recovery_tip') }}
                        </div>

                        <h3>{{ __('docs.security_passkeys_title') }}</h3>
                        <p>{{ __('docs.security_passkeys_intro') }}</p>
                        <div class="not-prose bg-gray-50 dark:bg-gray-700 rounded-lg p-4 my-4">
                            <ol class="space-y-2 text-sm">
                                <li class="flex gap-3"><span class="bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">1</span><span>{{ __('docs.security_passkeys_s1') }}</span></li>
                                <li class="flex gap-3"><span class="bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">2</span><span>{{ __('docs.security_passkeys_s2') }}</span></li>
                                <li class="flex gap-3"><span class="bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">3</span><span>{{ __('docs.security_passkeys_s3') }}</span></li>
                                <li class="flex gap-3"><span class="bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold flex-shrink-0">4</span><span>{{ __('docs.security_passkeys_s4') }}</span></li>
                            </ol>
                        </div>
                        <div class="not-prose bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-400 p-3 my-3 text-sm text-blue-800 dark:text-blue-200">
                            <strong>{{ __('docs.tip') }}:</strong> {{ __('docs.security_passkeys_hybrid_tip') }}
                        </div>

                        <h3>{{ __('docs.security_users_title') }}</h3>
                        <p>{{ __('docs.security_users_intro') }}</p>
                        <ul class="list-disc list-inside text-sm space-y-1 mt-2">
                            <li>{{ __('docs.security_users_action_reset_password') }}</li>
                            <li>{{ __('docs.security_users_action_disable') }}</li>
                            <li>{{ __('docs.security_users_action_disable_2fa') }}</li>
                            <li>{{ __('docs.security_users_action_reset_passkeys') }}</li>
                            <li>{{ __('docs.security_users_action_change_role') }}</li>
                            <li>{{ __('docs.security_users_action_delete') }}</li>
                        </ul>

                        <h3>{{ __('docs.security_impersonation_title') }}</h3>
                        <p>{{ __('docs.security_impersonation_desc') }}</p>

                    @elseif ($section === 'settings')
                        <h2>{{ __('docs.settings_title') }}</h2>
                        <p>{{ __('docs.settings_intro') }}</p>

                        <h3>{{ __('docs.settings_profile_title') }}</h3>
                        <p>{{ __('docs.settings_profile_desc') }}</p>

                        <h3>{{ __('docs.settings_language_title') }}</h3>
                        <p>{{ __('docs.settings_language_desc') }}</p>

                        <h3>{{ __('docs.settings_webhook_title') }}</h3>
                        <p>{{ __('docs.settings_webhook_desc') }}</p>

                    @elseif ($section === 'faq')
                        <h2>{{ __('docs.faq_title') }}</h2>

                        @foreach (__('docs.faqs') as $faq)
                            <div class="not-prose border-b border-gray-100 dark:border-gray-700 py-4">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $faq['q'] }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $faq['a'] }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
