<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center gap-2">
                {{ $project ? ($canEdit ? __('projects.edit_project') : __('projects.view_project')) . ': ' . $project->name : __('projects.new_project') }}
                @if($project && !$canEdit)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        {{ __('projects.read_only') }}
                    </span>
                @endif
            </h2>
            <a href="{{ route('projects.index') }}" wire:navigate class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                &larr; {{ __('projects.back_to_list') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        @if($project && !$canEdit)
            <div class="mb-4 flex items-center gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-300 px-4 py-3 rounded-md text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                {{ __('projects.read_only_notice') }}
            </div>
        @endif
        </div>
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8"
             x-data="{
                activeTab: (window.location.hash && window.location.hash.slice(1))
                    || localStorage.getItem('tab-project-{{ $project?->id ?? 'new' }}')
                    || 'basic'
             }"
             x-init="$watch('activeTab', v => {
                history.replaceState(null, '', '#' + v);
                localStorage.setItem('tab-project-{{ $project?->id ?? 'new' }}', v);
             })">
            <form wire:submit="save">
                {{-- Header with project type badge --}}
                <div class="bg-white dark:bg-gray-800 rounded-t-lg shadow-sm p-6">
                    <div class="flex justify-between items-start border-b-2 border-blue-600 pb-4">
                        <div>
                            <h1 class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ __('projects.status_report') }}</h1>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('projects.report_subtitle') }}</p>
                        </div>
                        <div class="text-right">
                            @php
                                $selectedType = $projectTypes->firstWhere('id', $project_type_id);
                                $selectClass  = $selectedType
                                    ? $selectedType->badgeClass()
                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border-gray-300 dark:border-gray-600';
                            @endphp
                            @if($canEdit)
                                <label class="block text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1 text-right">
                                    {{ __('projects.type') }}<span class="text-red-500 ml-0.5">*</span>
                                </label>
                                <select wire:model.live="project_type_id"
                                        aria-label="{{ __('projects.aria_project_type') }}"
                                        class="text-xs font-semibold rounded-full border px-3 py-1 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-400 {{ $selectClass }} {{ $errors->has('project_type_id') ? 'ring-2 ring-red-400' : '' }}">
                                    <option value="">{{ __('projects.type_placeholder') }}</option>
                                    @foreach($projectTypes as $pt)
                                        <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                                    @endforeach
                                </select>
                                @error('project_type_id')
                                    <p class="mt-1 text-xs text-red-500 text-right">{{ $message }}</p>
                                @enderror
                            @else
                                @if($selectedType)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold border {{ $selectClass }}">
                                        {{ $selectedType->name }}
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Tab navigation --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex flex-wrap gap-y-1 px-6 -mb-px">
                        <button type="button" @click="activeTab = 'basic'"
                                :class="activeTab === 'basic' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500'"
                                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                            {{ __('projects.tab_basic') }}
                        </button>
                        <button type="button" @click="activeTab = 'phases'"
                                :class="activeTab === 'phases' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500'"
                                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                            {{ __('projects.tab_phases') }}
                        </button>
                        <button type="button" @click="activeTab = 'estimation'"
                                :class="activeTab === 'estimation' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500'"
                                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                            {{ __('projects.tab_estimation') }}
                        </button>
                        <button type="button" @click="activeTab = 'risks'"
                                :class="activeTab === 'risks' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500'"
                                class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                            {{ __('projects.tab_risks') }}
                        </button>
                        @if ($project)
                            <button type="button" @click="activeTab = 'burndown'"
                                    :class="activeTab === 'burndown' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500'"
                                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                                {{ __('projects.tab_burndown') }}
                            </button>
                            <button type="button" @click="activeTab = 'comments'"
                                    :class="activeTab === 'comments' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500'"
                                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors">
                                {{ __('projects.comments_title') }}
                                @if ($comments->count() > 0)
                                    <span class="ml-1 px-1.5 py-0.5 bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300 rounded-full text-[10px]">{{ $comments->count() }}</span>
                                @endif
                            </button>
                        @endif
                    </nav>
                </div>

                {{-- TAB: Basic Info --}}
                <div x-show="activeTab === 'basic'" x-cloak class="bg-white dark:bg-gray-800 shadow-sm p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                            <label for="project-name" class="block text-[10px] uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">{{ __('projects.project_name') }} *</label>
                            <input type="text" id="project-name" wire:model="name" placeholder="{{ __('projects.project_name_placeholder') }}"
                                   @disabled(!$canEdit)
                                   class="w-full bg-transparent border-none text-sm text-gray-900 dark:text-gray-100 focus:ring-0 p-0 disabled:opacity-70 disabled:cursor-default">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                            <label for="project-client" class="block text-[10px] uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">{{ __('projects.client') }}</label>
                            <input type="text" id="project-client" wire:model="client" placeholder="{{ __('projects.client_placeholder') }}"
                                   @disabled(!$canEdit)
                                   class="w-full bg-transparent border-none text-sm text-gray-900 dark:text-gray-100 focus:ring-0 p-0 disabled:opacity-70 disabled:cursor-default">
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                            <label for="project-team-lead" class="block text-[10px] uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">{{ __('projects.team_lead') }}</label>
                            <input type="text" id="project-team-lead" wire:model="team_lead" placeholder="{{ __('projects.team_lead_placeholder') }}"
                                   @disabled(!$canEdit)
                                   class="w-full bg-transparent border-none text-sm text-gray-900 dark:text-gray-100 focus:ring-0 p-0 disabled:opacity-70 disabled:cursor-default">
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                            <label for="project-report-date" class="block text-[10px] uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">{{ __('projects.report_date') }}</label>
                            <input type="date" id="project-report-date" wire:model="report_date"
                                   @disabled(!$canEdit)
                                   class="w-full bg-transparent border-none text-sm text-gray-900 dark:text-gray-100 focus:ring-0 p-0 disabled:opacity-70 disabled:cursor-default">
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                            <label for="project-start" class="block text-[10px] uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">{{ __('projects.project_start') }}</label>
                            <input type="date" id="project-start" wire:model="project_start"
                                   @disabled(!$canEdit)
                                   class="w-full bg-transparent border-none text-sm text-gray-900 dark:text-gray-100 focus:ring-0 p-0 disabled:opacity-70 disabled:cursor-default">
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                            <label for="project-go-live" class="block text-[10px] uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">{{ __('projects.planned_go_live') }}</label>
                            <input type="date" id="project-go-live" wire:model="planned_go_live"
                                   @disabled(!$canEdit)
                                   class="w-full bg-transparent border-none text-sm text-gray-900 dark:text-gray-100 focus:ring-0 p-0 disabled:opacity-70 disabled:cursor-default">
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                            <label for="project-phase" class="block text-[10px] uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">{{ __('projects.current_phase') }}</label>
                            <select id="project-phase" wire:model="current_phase" @disabled(!$canEdit) class="w-full bg-transparent border-none text-sm text-gray-900 dark:text-gray-100 focus:ring-0 p-0 disabled:opacity-70 disabled:cursor-default">
                                @foreach (\App\Models\Project::getPhaseLabels() as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                            <label for="project-health" class="block text-[10px] uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-1">{{ __('projects.overall_health') }}</label>
                            <select id="project-health" wire:model="overall_health" @disabled(!$canEdit) class="w-full bg-transparent border-none text-sm text-gray-900 dark:text-gray-100 focus:ring-0 p-0 disabled:opacity-70 disabled:cursor-default">
                                <option value="on_track">🟢 {{ __('projects.health_on_track') }}</option>
                                <option value="at_risk">🟡 {{ __('projects.health_at_risk') }}</option>
                                <option value="off_track">🔴 {{ __('projects.health_off_track') }}</option>
                            </select>
                        </div>
                    </div>

                    {{-- Footer fields --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="flex flex-wrap justify-between gap-4 text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-2">
                                <label for="filled-by">{{ __('projects.filled_by') }}:</label>
                                <input type="text" id="filled-by" wire:model="filled_by" placeholder="{{ __('projects.filled_by_placeholder') }}"
                                       @disabled(!$canEdit)
                                       class="border-b border-gray-300 dark:border-gray-600 text-xs text-gray-600 dark:text-gray-400 bg-transparent focus:ring-0 focus:border-blue-500 px-1 py-0.5 w-full sm:w-36 disabled:opacity-70 disabled:cursor-default">
                            </div>
                            <div class="flex items-center gap-2">
                                <label for="reviewed-by">{{ __('projects.reviewed_by') }}:</label>
                                <input type="text" id="reviewed-by" wire:model="reviewed_by" placeholder="{{ __('projects.reviewed_by_placeholder') }}"
                                       @disabled(!$canEdit)
                                       class="border-b border-gray-300 dark:border-gray-600 text-xs text-gray-600 dark:text-gray-400 bg-transparent focus:ring-0 focus:border-blue-500 px-1 py-0.5 w-full sm:w-36 disabled:opacity-70 disabled:cursor-default">
                            </div>
                            <div class="flex items-center gap-2">
                                <label for="project-version">{{ __('projects.version') }}:</label>
                                <input type="text" id="project-version" wire:model="version" placeholder="v1.0"
                                       @disabled(!$canEdit)
                                       class="border-b border-gray-300 dark:border-gray-600 text-xs text-gray-600 dark:text-gray-400 bg-transparent focus:ring-0 focus:border-blue-500 px-1 py-0.5 w-20 disabled:opacity-70 disabled:cursor-default">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: Phases --}}
                <div x-show="activeTab === 'phases'" x-cloak class="bg-white dark:bg-gray-800 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold">{{ __('projects.phases_title') }}</h2>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 hidden sm:inline">{{ __('projects.drag_to_reorder') }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-blue-600 text-white">
                                    <th class="w-6 px-2 py-2"></th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold">{{ __('projects.phase') }}</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold">{{ __('projects.key_activities') }}</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold">{{ __('projects.client_confirmation') }}</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold">{{ __('projects.status') }}</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold">{{ __('projects.date') }}</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold">{{ __('projects.notes') }}</th>
                                    <th class="w-6 px-2 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="phases-sortable">
                                @foreach ($phases as $i => $phase)
                                    <tr wire:key="phase-{{ $i }}" data-phase-index="{{ $i }}"
                                        class="phase-row border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-2 py-2 align-middle text-center">
                                            @if($canEdit)
                                            <span class="phase-drag-handle cursor-move text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 select-none" title="{{ __('projects.drag_to_reorder') }}">
                                                <svg class="w-4 h-4 inline-block" fill="currentColor" viewBox="0 0 20 20"><path d="M7 4a1 1 0 11-2 0 1 1 0 012 0zm0 6a1 1 0 11-2 0 1 1 0 012 0zm0 6a1 1 0 11-2 0 1 1 0 012 0zm8-12a1 1 0 11-2 0 1 1 0 012 0zm0 6a1 1 0 11-2 0 1 1 0 012 0zm0 6a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                                            </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" wire:model="phases.{{ $i }}.phase_name"
                                                   aria-label="{{ __('projects.aria_phase_name') }}"
                                                   placeholder="{{ __('projects.phase_name_placeholder') }}"
                                                   @disabled(!$canEdit)
                                                   class="w-full text-xs font-medium border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 disabled:opacity-70 disabled:cursor-default">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" wire:model="phases.{{ $i }}.key_activities"
                                                   aria-label="{{ __('projects.aria_phase_activities') }}"
                                                   placeholder="{{ __('projects.phase_activities_placeholder') }}"
                                                   @disabled(!$canEdit)
                                                   class="w-full text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 disabled:opacity-70 disabled:cursor-default">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" wire:model="phases.{{ $i }}.client_confirmation"
                                                   aria-label="{{ __('projects.aria_phase_client') }}"
                                                   placeholder="{{ __('projects.phase_client_placeholder') }}"
                                                   @disabled(!$canEdit)
                                                   class="w-full text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 disabled:opacity-70 disabled:cursor-default">
                                        </td>
                                        <td class="px-3 py-2">
                                            <select wire:model="phases.{{ $i }}.status"
                                                    aria-label="{{ __('projects.aria_phase_status') }}"
                                                    @disabled(!$canEdit)
                                                    class="text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 dark:text-gray-200 disabled:opacity-70 disabled:cursor-default">
                                                <option value="pending">⚪ {{ __('projects.status_pending') }}</option>
                                                <option value="in_progress">🔵 {{ __('projects.status_in_progress') }}</option>
                                                <option value="done">✅ {{ __('projects.status_done') }}</option>
                                                <option value="blocked">🔴 {{ __('projects.status_blocked') }}</option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="date" wire:model="phases.{{ $i }}.completion_date"
                                                   aria-label="{{ __('projects.aria_phase_date') }}"
                                                   @disabled(!$canEdit)
                                                   class="text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 disabled:opacity-70 disabled:cursor-default">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" wire:model="phases.{{ $i }}.notes" placeholder="..."
                                                   aria-label="{{ __('projects.aria_phase_notes') }}"
                                                   @disabled(!$canEdit)
                                                   class="w-full text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 disabled:opacity-70 disabled:cursor-default">
                                        </td>
                                        <td class="px-2 py-2 align-middle text-center">
                                            @if($canEdit)
                                            <button type="button" wire:click="removePhase({{ $i }})"
                                                    class="text-red-400 hover:text-red-600 text-xs px-1"
                                                    aria-label="{{ __('projects.aria_remove_phase') }}"
                                                    title="{{ __('projects.remove_phase') }}">&times;</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($canEdit)
                    <button type="button" wire:click="addPhase" class="mt-3 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        {{ __('projects.add_phase') }}
                    </button>
                    @endif
                </div>

                {{-- TAB: Estimation --}}
                <div x-show="activeTab === 'estimation'" x-cloak class="bg-white dark:bg-gray-800 shadow-sm p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Estimation --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                            <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-3">{{ __('projects.estimation_title') }}</h3>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="text-center bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                                    <label for="estimated-hours" class="block text-[10px] text-gray-600 dark:text-gray-300 mb-1">{{ __('projects.estimated_hours') }}</label>
                                    <input type="number" id="estimated-hours" wire:model.live="estimated_hours" placeholder="0"
                                           @disabled(!$canEdit)
                                           class="w-full text-center text-sm font-semibold border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 disabled:opacity-70 disabled:cursor-default">
                                </div>
                                <div class="text-center bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                                    <label for="spent-hours" class="block text-[10px] text-gray-600 dark:text-gray-300 mb-1">{{ __('projects.spent_hours') }}</label>
                                    <input type="number" id="spent-hours" wire:model.live="spent_hours" placeholder="0"
                                           @disabled(!$canEdit)
                                           class="w-full text-center text-sm font-semibold border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 disabled:opacity-70 disabled:cursor-default">
                                </div>
                                <div class="text-center bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                                    <label for="remaining-hours" class="block text-[10px] text-gray-600 dark:text-gray-300 mb-1">{{ __('projects.remaining_hours') }}</label>
                                    <input type="number" id="remaining-hours" wire:model.live="remaining_hours" placeholder="0"
                                           @disabled(!$canEdit)
                                           class="w-full text-center text-sm font-semibold border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 disabled:opacity-70 disabled:cursor-default">
                                </div>
                            </div>

                            @if ($estimated_hours)
                                @php
                                    $forecast = ($spent_hours ?? 0) + ($remaining_hours ?? 0);
                                    $delta = $forecast - $estimated_hours;
                                    $pct = $estimated_hours > 0 ? round(($delta / $estimated_hours) * 100) : 0;
                                @endphp
                                <div class="mt-3 text-center text-xs p-2 rounded-md
                                    @if ($delta <= 0) bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                    @elseif ($pct <= 15) bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                                    @else bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 @endif">
                                    @if ($delta <= 0)
                                        {{ __('projects.delta_ok', ['forecast' => $forecast, 'pct' => abs($pct)]) }}
                                    @elseif ($pct <= 15)
                                        {{ __('projects.delta_warn', ['delta' => $delta, 'pct' => $pct]) }}
                                    @else
                                        {{ __('projects.delta_over', ['delta' => $delta, 'pct' => $pct]) }}
                                    @endif
                                </div>
                            @else
                                <div class="mt-3 text-center text-xs text-gray-400 dark:text-gray-500 p-2">{{ __('projects.enter_hours') }}</div>
                            @endif

                            <div class="mt-3">
                                <label for="estimation-comment" class="block text-xs text-gray-500 dark:text-gray-400 font-semibold mb-1">{{ __('projects.estimation_comment') }}</label>
                                <textarea id="estimation-comment" wire:model="estimation_comment" rows="2" placeholder="{{ __('projects.estimation_comment_placeholder') }}"
                                          @disabled(!$canEdit)
                                          class="w-full text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 resize-none disabled:opacity-70 disabled:cursor-default"></textarea>
                            </div>
                        </div>

                        {{-- Next Steps --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-5">
                            <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-3">{{ __('projects.next_steps_title') }}</h3>
                            <ul class="space-y-2">
                                @foreach ($nextSteps as $i => $step)
                                    <li class="flex items-start gap-2">
                                        <input type="checkbox" wire:model="nextSteps.{{ $i }}.is_completed"
                                               aria-label="{{ __('projects.aria_step_completed') }}"
                                               @disabled(!$canEdit)
                                               class="mt-1 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 focus:ring-blue-500 disabled:opacity-70 disabled:cursor-default">
                                        <input type="text" wire:model="nextSteps.{{ $i }}.description"
                                               aria-label="{{ __('projects.aria_step_description') }}"
                                               placeholder="{{ __('projects.step_placeholder', ['number' => $i + 1]) }}"
                                               @disabled(!$canEdit)
                                               class="flex-1 text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1 disabled:opacity-70 disabled:cursor-default">
                                        @if($canEdit)
                                        <button type="button" wire:click="removeNextStep({{ $i }})"
                                                aria-label="{{ __('projects.aria_remove_step') }}"
                                                class="text-red-400 hover:text-red-600 text-xs">&times;</button>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            @if($canEdit)
                            <button type="button" wire:click="addNextStep" class="mt-2 text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ __('projects.add_step') }}
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- TAB: Risks & Notifications --}}
                <div x-show="activeTab === 'risks'" x-cloak class="bg-white dark:bg-gray-800 shadow-sm p-6">
                    {{-- Risks --}}
                    <div class="mb-6">
                        <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-3">{{ __('projects.risks_title') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr_2fr_auto] gap-2 text-[10px] uppercase text-gray-500 dark:text-gray-400 font-semibold mb-2 px-1">
                            <span>{{ __('projects.risk_description') }}</span>
                            <span>{{ __('projects.risk_level') }}</span>
                            <span>{{ __('projects.risk_mitigation') }}</span>
                            <span></span>
                        </div>
                        @foreach ($risks as $i => $risk)
                            <div class="grid grid-cols-1 md:grid-cols-[2fr_1fr_2fr_auto] gap-2 mb-2">
                                <input type="text" wire:model="risks.{{ $i }}.description"
                                       aria-label="{{ __('projects.aria_risk_description') }}"
                                       placeholder="{{ __('projects.risk_description_placeholder') }}"
                                       @disabled(!$canEdit)
                                       class="text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1.5 disabled:opacity-70 disabled:cursor-default">
                                <select wire:model="risks.{{ $i }}.level"
                                        aria-label="{{ __('projects.aria_risk_level') }}"
                                        @disabled(!$canEdit)
                                        class="text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1.5 disabled:opacity-70 disabled:cursor-default">
                                    <option value="low">🟢 {{ __('projects.level_low') }}</option>
                                    <option value="medium">🟡 {{ __('projects.level_medium') }}</option>
                                    <option value="high">🔴 {{ __('projects.level_high') }}</option>
                                </select>
                                <input type="text" wire:model="risks.{{ $i }}.mitigation"
                                       aria-label="{{ __('projects.aria_risk_mitigation') }}"
                                       placeholder="{{ __('projects.risk_mitigation_placeholder') }}"
                                       @disabled(!$canEdit)
                                       class="text-xs border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded px-2 py-1.5 disabled:opacity-70 disabled:cursor-default">
                                @if($canEdit)
                                <button type="button" wire:click="removeRisk({{ $i }})"
                                        aria-label="{{ __('projects.aria_remove_risk') }}"
                                        class="text-red-400 hover:text-red-600 text-xs px-1">&times;</button>
                                @endif
                            </div>
                        @endforeach
                        @if($canEdit)
                        <button type="button" wire:click="addRisk" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            {{ __('projects.add_risk') }}
                        </button>
                        @endif
                    </div>

                    {{-- Product notification --}}
                    <div>
                        <h2 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-3">{{ __('projects.notification_title') }}</h2>
                        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-400 dark:border-yellow-700 rounded-md p-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label for="notification-deadline" class="block text-xs text-yellow-800 dark:text-yellow-400 font-semibold mb-1">{{ __('projects.notification_deadline') }}</label>
                                <input type="date" id="notification-deadline" wire:model="product_notification_deadline"
                                       @disabled(!$canEdit)
                                       class="w-full text-xs border border-yellow-400 dark:border-yellow-700 rounded px-2 py-1.5 bg-white dark:bg-gray-700 dark:text-gray-200 disabled:opacity-70 disabled:cursor-default">
                            </div>
                            <div>
                                <label for="notification-duration" class="block text-xs text-yellow-800 dark:text-yellow-400 font-semibold mb-1">{{ __('projects.notification_duration') }}</label>
                                <input type="text" id="notification-duration" wire:model="product_notification_duration" placeholder="{{ __('projects.notification_duration_placeholder') }}"
                                       @disabled(!$canEdit)
                                       class="w-full text-xs border border-yellow-400 dark:border-yellow-700 rounded px-2 py-1.5 bg-white dark:bg-gray-700 dark:text-gray-200 disabled:opacity-70 disabled:cursor-default">
                            </div>
                            <div class="md:col-span-2">
                                <label for="notification-description" class="block text-xs text-yellow-800 dark:text-yellow-400 font-semibold mb-1">{{ __('projects.notification_description') }}</label>
                                <textarea id="notification-description" wire:model="product_notification_description" rows="2"
                                          placeholder="{{ __('projects.notification_description_placeholder') }}"
                                          @disabled(!$canEdit)
                                          class="w-full text-xs border border-yellow-400 dark:border-yellow-700 rounded px-2 py-1.5 bg-white dark:bg-gray-700 dark:text-gray-200 resize-none disabled:opacity-70 disabled:cursor-default"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: Burndown Chart (only for existing projects) --}}
                @if ($project)
                    <div x-show="activeTab === 'burndown'" x-cloak class="bg-white dark:bg-gray-800 shadow-sm p-6">
                        <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-4">{{ __('projects.burndown_title') }}</h3>

                        @if (count($burndownData) < 2)
                            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                                <div class="text-3xl mb-2">📉</div>
                                <p class="text-sm">{{ __('projects.burndown_no_data') }}</p>
                            </div>
                        @else
                            @php
                                $burndownLabels = array_column($burndownData, 'date');
                                $burndownEst = array_column($burndownData, 'estimated');
                                $burndownSpent = array_column($burndownData, 'spent');
                                $burndownRemain = array_column($burndownData, 'remaining');
                            @endphp
                            <div wire:ignore class="h-72"
                                 x-data="burndownChart({
                                     labels: @js($burndownLabels),
                                     estimated: @js($burndownEst),
                                     spent: @js($burndownSpent),
                                     remaining: @js($burndownRemain),
                                     estimatedLabel: @js(__('projects.estimated_hours')),
                                     spentLabel: @js(__('projects.spent_hours')),
                                     remainingLabel: @js(__('projects.remaining_hours')),
                                 })" x-init="render()">
                                <canvas x-ref="canvas"></canvas>
                            </div>
                            <script>
                                if (typeof window.burndownChart === 'undefined') {
                                    window.burndownChart = function (data) {
                                        return {
                                            chart: null,
                                            render() {
                                                if (this.chart) this.chart.destroy();
                                                const ctx = this.$refs.canvas;
                                                this.chart = new window.Chart(ctx, {
                                                    type: 'line',
                                                    data: {
                                                        labels: data.labels,
                                                        datasets: [
                                                            { label: data.estimatedLabel, data: data.estimated, borderColor: '#94a3b8', borderDash: [6, 4], pointRadius: 0, fill: false, tension: 0 },
                                                            { label: data.spentLabel, data: data.spent, borderColor: '#ef4444', backgroundColor: '#ef4444', pointRadius: 3, fill: false, tension: 0.2 },
                                                            { label: data.remainingLabel, data: data.remaining, borderColor: '#3b82f6', backgroundColor: '#3b82f6', pointRadius: 3, fill: false, tension: 0.2 },
                                                        ],
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        plugins: {
                                                            legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 14 } },
                                                        },
                                                        scales: {
                                                            x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 0, autoSkip: true } },
                                                            y: { beginAtZero: true, ticks: { font: { size: 10 }, callback: (v) => v + 'h' } },
                                                        },
                                                    },
                                                });
                                            },
                                        };
                                    };
                                }
                            </script>
                        @endif
                    </div>
                @endif

                {{-- TAB: Comments (only for existing projects) --}}
                @if ($project)
                    <div x-show="activeTab === 'comments'" x-cloak class="bg-white dark:bg-gray-800 shadow-sm p-6">
                        <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-4">{{ __('projects.comments_title') }}</h3>

                        {{-- New comment form --}}
                        <div class="mb-4 flex gap-2">
                            <textarea wire:model="newComment" rows="2" placeholder="{{ __('projects.comment_placeholder') }}"
                                      aria-label="{{ __('projects.aria_comment') }}"
                                      class="flex-1 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-2 resize-none"></textarea>
                            <button type="button" wire:click="addComment"
                                    class="self-end px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition">
                                {{ __('projects.comment_add') }}
                            </button>
                        </div>

                        {{-- Comments list --}}
                        @if ($comments->isEmpty())
                            <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">{{ __('projects.no_comments') }}</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($comments as $comment)
                                    <div class="flex gap-3 p-3 rounded-md bg-gray-50 dark:bg-gray-700">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ mb_substr($comment->user->name ?? '?', 0, 1) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ $comment->user->name ?? 'System' }}</span>
                                                <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-gray-700 dark:text-gray-200">{{ $comment->body }}</p>
                                            @if ($comment->user_id === auth()->id() || auth()->user()->isAdmin())
                                                <button type="button" wire:click="deleteComment({{ $comment->id }})"
                                                        wire:confirm="{{ __('projects.confirm_delete') }}"
                                                        class="text-[10px] text-red-400 hover:text-red-600 mt-1">
                                                    {{ __('projects.comment_delete') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Submit (always visible) --}}
                <div class="bg-white dark:bg-gray-800 rounded-b-lg shadow-sm p-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('projects.index') }}" wire:navigate
                           class="px-6 py-2.5 border border-gray-300 dark:border-gray-600 rounded-md text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('projects.cancel') }}
                        </a>
                        @if($canEdit)
                        <button type="submit"
                                class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ __('projects.save') }}</span>
                            <span wire:loading>{{ __('projects.saving') }}</span>
                        </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@script
<script>
    (function initPhasesSortable() {
        if (typeof window.Sortable === 'undefined') {
            setTimeout(initPhasesSortable, 100);
            return;
        }

        var componentId = $wire.id;

        function attach() {
            if (!$wire.el || !document.body.contains($wire.el)) return;
            var tbody = $wire.el.querySelector('.phases-sortable');
            if (!tbody || tbody._sortableInstance) return;

            tbody._sortableInstance = window.Sortable.create(tbody, {
                animation: 150,
                handle: '.phase-drag-handle',
                draggable: '.phase-row',
                ghostClass: 'opacity-30',
                dragClass: 'shadow-lg',
                onEnd: function () {
                    var rows = tbody.querySelectorAll('.phase-row');
                    var order = Array.prototype.map.call(rows, function (r) {
                        return r.dataset.phaseIndex;
                    });
                    $wire.reorderPhases(order);
                }
            });
        }

        attach();

        // Re-attach after Livewire re-renders THIS component (scoped by component id
        // to avoid touching other components, and guarded against stale $wire after
        // wire:navigate destroys this one).
        Livewire.hook('morph.updated', function (payload) {
            try {
                var component = payload && payload.component;
                if (!component || component.id !== componentId) return;
                if (!$wire.el || !document.body.contains($wire.el)) return;
                var tbody = $wire.el.querySelector('.phases-sortable');
                if (tbody && !tbody._sortableInstance) {
                    attach();
                }
            } catch (e) { /* component gone, ignore */ }
        });
    })();
</script>
@endscript
