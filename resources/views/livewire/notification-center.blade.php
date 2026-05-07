<div class="relative" wire:poll.60s>
    {{-- Bell Icon --}}
    <button wire:click="toggleDropdown" type="button"
            aria-label="{{ __('dashboard.aria_notifications') }}"
            class="relative inline-flex items-center p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none transition">
        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>

        {{-- Unread Badge --}}
        @if($this->unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                {{ $this->unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    @if($showDropdown)
        <div class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 z-50">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('dashboard.notifications') }}</h3>
                @if($this->unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                        {{ __('dashboard.mark_all_read') }}
                    </button>
                @endif
            </div>

            {{-- Notification List --}}
            <div class="max-h-96 overflow-y-auto">
                @forelse($notifications as $notification)
                    <button
                        wire:click="markAsRead('{{ $notification->id }}')"
                        class="w-full text-left px-4 py-3 border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ is_null($notification->read_at) ? 'bg-indigo-50 dark:bg-indigo-900/30' : '' }}"
                    >
                        <div class="flex items-start gap-3">
                            {{-- Icon based on type --}}
                            <div class="flex-shrink-0 mt-0.5">
                                @if($notification->type === \App\Notifications\ProjectAlertNotification::class)
                                    <svg class="h-5 w-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-gray-100 {{ is_null($notification->read_at) ? 'font-semibold' : '' }}">
                                    @if($notification->type === \App\Notifications\ProjectAlertNotification::class)
                                        @php $data = $notification->data; @endphp
                                        @if(($data['alert_type'] ?? $data['type'] ?? '') === 'off_track')
                                            {{ __('dashboard.alert_off_track', ['name' => $data['project_name'] ?? '']) }}
                                        @elseif(($data['alert_type'] ?? $data['type'] ?? '') === 'go_live_soon')
                                            {{ __('dashboard.alert_go_live_soon', ['name' => $data['project_name'] ?? '', 'days' => $data['days'] ?? '?']) }}
                                        @elseif(($data['alert_type'] ?? $data['type'] ?? '') === 'budget_overrun')
                                            {{ __('dashboard.alert_budget_overrun', ['name' => $data['project_name'] ?? '', 'pct' => $data['pct'] ?? '?']) }}
                                        @elseif(($data['alert_type'] ?? $data['type'] ?? '') === 'health_changed')
                                            {{ __('dashboard.alert_health_changed', ['name' => $data['project_name'] ?? '']) }}
                                        @else
                                            {{ $data['message'] ?? __('dashboard.notifications') }}
                                        @endif
                                    @elseif($notification->type === \App\Notifications\WeeklyReportNotification::class)
                                        {{ __('dashboard.weekly_report') }}
                                    @else
                                        {{ $notification->data['message'] ?? __('dashboard.notifications') }}
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>

                            {{-- Unread indicator --}}
                            @if(is_null($notification->read_at))
                                <div class="flex-shrink-0 mt-1">
                                    <span class="inline-block h-2 w-2 rounded-full bg-indigo-500"></span>
                                </div>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ __('dashboard.no_notifications') }}
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
