<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('projects.title_list') }}</h2>
            <div class="flex flex-wrap items-center gap-2 gap-y-2">
                <a href="{{ route('projects.export', ['format' => 'xlsx', 'health' => $filterHealth, 'type' => $filterType]) }}"
                   class="hidden sm:inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    📊 {{ __('projects.export_excel') }}
                </a>
                <a href="{{ route('projects.export', ['format' => 'csv', 'health' => $filterHealth, 'type' => $filterType]) }}"
                   class="hidden sm:inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    📄 {{ __('projects.export_csv') }}
                </a>
                <a href="{{ route('projects.create') }}" wire:navigate
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                    {{ __('projects.new_project') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-md">
                    {{ session('message') }}
                </div>
            @endif

            {{-- View switcher + Filters --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="flex items-center gap-1 px-4 pt-3 pb-2 border-b border-gray-100 dark:border-gray-700">

                    <a href="{{ route('projects.index') }}"
                       class="px-3 py-1.5 text-xs font-semibold rounded-md bg-blue-600 text-white">
                        {{ __('projects.view_list') }}
                    </a>
                    <a href="{{ route('projects.kanban') }}" wire:navigate
                       class="px-3 py-1.5 text-xs font-medium rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        {{ __('projects.view_kanban') }}
                    </a>
                    <a href="{{ route('projects.timeline') }}" wire:navigate
                       class="px-3 py-1.5 text-xs font-medium rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        {{ __('projects.view_timeline') }}
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
                    <div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                               aria-label="{{ __('projects.aria_search_projects') }}"
                               placeholder="{{ __('projects.search_placeholder') }}"
                               class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <select wire:model.live="filterHealth" aria-label="{{ __('projects.aria_filter_health') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('projects.all_statuses') }}</option>
                            <option value="on_track">🟢 {{ __('projects.health_on_track') }}</option>
                            <option value="at_risk">🟡 {{ __('projects.health_at_risk') }}</option>
                            <option value="off_track">🔴 {{ __('projects.health_off_track') }}</option>
                        </select>
                    </div>
                    <div>
                        <select wire:model.live="filterType" aria-label="{{ __('projects.aria_filter_type') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('projects.all_types') }}</option>
                            @foreach($projectTypes as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Row colour legend --}}
            <div class="mb-3 flex flex-wrap gap-2 text-xs">
                <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-red-50 dark:bg-red-900/20 border-l-4 border-l-red-500 text-red-700 dark:text-red-300">
                    {{ __('projects.legend_overdue') }}
                </span>
                <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-amber-50 dark:bg-amber-900/20 border-l-4 border-l-amber-400 text-amber-700 dark:text-amber-300">
                    {{ __('projects.legend_soon') }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <span class="text-amber-500">⚠</span>{{ __('projects.legend_stale') }}
                </span>
            </div>

            {{-- Project list --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('projects.header_project') }}</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('projects.header_client') }}</th>
                            <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('projects.header_type') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('projects.header_phase') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('projects.header_health') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('projects.header_go_live') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-px whitespace-nowrap">{{ __('projects.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($projects as $project)
                            @php
                                $isOverdue  = $project->planned_go_live
                                    && $project->planned_go_live->isPast()
                                    && $project->current_phase !== 'hypercare';
                                $isSoon     = !$isOverdue
                                    && $project->planned_go_live
                                    && $project->planned_go_live->isFuture()
                                    && $project->planned_go_live->diffInDays(now()) <= 14;
                                $isStale    = $project->updated_at->diffInDays(now()) >= 14;

                                $rowClass = match(true) {
                                    $editingId === $project->id => 'bg-blue-50 dark:bg-blue-900/30',
                                    $isOverdue  => 'bg-red-50 dark:bg-red-900/20 border-l-4 border-l-red-500',
                                    $isSoon     => 'bg-amber-50 dark:bg-amber-900/20 border-l-4 border-l-amber-400',
                                    default     => '',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $rowClass }}">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $project->name }}
                                        @if($isStale)
                                            <span title="{{ __('projects.stale_tooltip') }}">
                                                <svg class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                            </span>
                                        @endif
                                        @cannot('update', $project)
                                            <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" title="{{ __('projects.read_only') }}"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        @endcannot
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('projects.team_lead') }}: {{ $project->team_lead ?? '-' }}</div>
                                </td>
                                <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ $project->client ?? '-' }}</td>
                                <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap">
                                    @if ($project->projectType)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $project->projectType->badgeClass() }}">
                                            {{ $project->projectType->name }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    @if ($editingId === $project->id)
                                        <select wire:change="saveInlineEdit({{ $project->id }}, 'current_phase', $event.target.value)"
                                                aria-label="{{ __('projects.aria_inline_edit_phase') }}"
                                                class="text-xs border border-blue-300 dark:border-blue-600 rounded px-2 py-1 bg-white dark:bg-gray-700 dark:text-gray-200">
                                            @foreach (\App\Models\Project::getPhaseLabels() as $val => $label)
                                                <option value="{{ $val }}" {{ $project->current_phase === $val ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{ \App\Models\Project::phaseLabel($project->current_phase) }}
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if ($editingId === $project->id)
                                        <select wire:change="saveInlineEdit({{ $project->id }}, 'overall_health', $event.target.value)"
                                                aria-label="{{ __('projects.aria_inline_edit_health') }}"
                                                class="text-xs border border-blue-300 dark:border-blue-600 rounded px-2 py-1 bg-white dark:bg-gray-700 dark:text-gray-200">
                                            <option value="on_track" {{ $project->overall_health === 'on_track' ? 'selected' : '' }}>🟢 {{ __('projects.health_on_track') }}</option>
                                            <option value="at_risk" {{ $project->overall_health === 'at_risk' ? 'selected' : '' }}>🟡 {{ __('projects.health_at_risk') }}</option>
                                            <option value="off_track" {{ $project->overall_health === 'off_track' ? 'selected' : '' }}>🔴 {{ __('projects.health_off_track') }}</option>
                                        </select>
                                    @else
                                        @php
                                            $healthIcon = match($project->overall_health) {
                                                'on_track' => '🟢',
                                                'at_risk' => '🟡',
                                                'off_track' => '🔴',
                                            };
                                        @endphp
                                        <span class="text-sm text-gray-900 dark:text-gray-100">{{ $healthIcon }} {{ __('projects.health_' . $project->overall_health) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm {{ $isOverdue ? 'text-red-600 dark:text-red-400 font-semibold' : ($isSoon ? 'text-amber-600 dark:text-amber-400 font-semibold' : 'text-gray-600 dark:text-gray-400') }}">
                                    {{ $project->planned_go_live?->format('d.m.Y') ?? '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm w-px">
                                    <div class="inline-flex items-center gap-1">
                                        @can('update', $project)
                                            @if ($editingId === $project->id)
                                                <button wire:click="cancelInlineEdit" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 text-xs px-2 py-1">{{ __('projects.cancel') }}</button>
                                            @else
                                                <button wire:click="startInlineEdit({{ $project->id }})"
                                                        aria-label="{{ __('projects.inline_edit') }}"
                                                        class="p-1.5 rounded text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 dark:hover:bg-yellow-900/30" title="{{ __('projects.inline_edit') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </button>
                                            @endif
                                        @endcan
                                        @can('update', $project)
                                            <a href="{{ route('projects.edit', $project) }}" wire:navigate
                                               aria-label="{{ __('projects.edit') }}"
                                               class="p-1.5 rounded text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/30" title="{{ __('projects.edit') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                        @else
                                            <a href="{{ route('projects.edit', $project) }}" wire:navigate
                                               aria-label="{{ __('projects.view') }}"
                                               class="p-1.5 rounded text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700" title="{{ __('projects.view') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                        @endcan
                                        <a href="{{ route('projects.history', $project) }}" wire:navigate
                                           aria-label="{{ __('projects.history') }}"
                                           class="p-1.5 rounded text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" title="{{ __('projects.history') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </a>
                                        <a href="{{ route('projects.pdf', $project) }}" target="_blank"
                                           aria-label="{{ __('projects.pdf') }}"
                                           class="p-1.5 rounded text-green-600 hover:text-green-800 hover:bg-green-50 dark:hover:bg-green-900/30" title="{{ __('projects.pdf') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                        @can('delete', $project)
                                            <button wire:click="deleteProject({{ $project->id }})"
                                                    wire:confirm="{{ __('projects.confirm_delete') }}"
                                                    aria-label="{{ __('projects.delete') }}"
                                                    class="p-1.5 rounded text-red-600 hover:text-red-800 hover:bg-red-50 dark:hover:bg-red-900/30" title="{{ __('projects.delete') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <p class="text-lg">{{ __('projects.no_projects') }}</p>
                                    <p class="text-sm mt-1">{{ __('projects.no_projects_hint') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $projects->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
