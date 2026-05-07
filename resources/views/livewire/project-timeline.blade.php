<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('projects.title_timeline') }}</h2>
    </x-slot>

    <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"
         x-data="{
             showPhases: localStorage.getItem('tl_phases') === '1',
             toggle() { this.showPhases = !this.showPhases; localStorage.setItem('tl_phases', this.showPhases ? '1' : '0'); }
         }">
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3">
        <div class="flex flex-wrap gap-2 items-center justify-between">
            <div class="flex items-center gap-1">
                <a href="{{ route('projects.index') }}" wire:navigate
                   class="px-3 py-1.5 text-xs font-medium rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    {{ __('projects.view_list') }}
                </a>
                <a href="{{ route('projects.kanban') }}" wire:navigate
                   class="px-3 py-1.5 text-xs font-medium rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    {{ __('projects.view_kanban') }}
                </a>
                <a href="{{ route('projects.timeline') }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-md bg-blue-600 text-white">
                    {{ __('projects.view_timeline') }}
                </a>
            </div>
            <div class="flex items-center gap-2">
                <button @click="toggle()"
                        :class="showPhases ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="px-2.5 py-1 text-xs rounded border transition">
                    {{ __('projects.tab_phases') }}
                </button>
                <div class="w-px h-4 bg-gray-200 dark:bg-gray-600"></div>
                <button wire:click="zoomOut" @disabled($zoom <= 1)
                        aria-label="{{ __('projects.zoom_out') }}"
                        class="px-2 py-1 text-xs rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition"
                        title="{{ __('projects.zoom_out') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
                </button>
                <span class="text-[10px] text-gray-500 dark:text-gray-400 px-1 min-w-[60px] text-center">
                    @if ($zoom === 1) {{ __('projects.zoom_quarters') }}
                    @elseif ($zoom === 2) {{ __('projects.zoom_months') }}
                    @else {{ __('projects.zoom_weeks') }}
                    @endif
                </span>
                <button wire:click="zoomIn" @disabled($zoom >= 3)
                        aria-label="{{ __('projects.zoom_in') }}"
                        class="px-2 py-1 text-xs rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition"
                        title="{{ __('projects.zoom_in') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                </button>
            </div>
        </div>
    </div>
    @if ($projects->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center text-gray-400">
            <p>{{ __('projects.no_projects') }}</p>
        </div>
    @else
        @php
            $start = $minDate->copy()->startOfMonth();
            $end = $maxDate->copy()->endOfMonth();
            $totalMonthDays = (int) $start->diffInDays($end) ?: 1;
            $todayOffset = max(0, min(100, ((int) $start->diffInDays(now()) / $totalMonthDays) * 100));
            $minWidth = match($zoom) {
                1 => 600,
                2 => 800,
                3 => max(1200, count($periods) * 80),
            };
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 overflow-x-auto"
             wire:key="timeline-{{ $zoom }}"
             x-init="$nextTick(() => {
                 let pct = {{ round($todayOffset, 2) }};
                 if (pct > 0) {
                     let inner = $el.scrollWidth;
                     let visible = $el.clientWidth;
                     if (inner > visible) {
                         $el.scrollLeft = Math.max(0, (pct / 100) * inner - visible / 2);
                     }
                 }
             })">
            <div style="min-width: {{ $minWidth }}px;">
                {{-- Time period headers --}}
                <div class="flex border-b border-gray-200 dark:border-gray-700 mb-4">
                    @foreach ($periods as $period)
                        @php
                            $pStart = $period['start']->copy();
                            $pEnd = $period['end']->copy();
                            // Clamp to timeline range
                            if ($pStart->lt($start)) $pStart = $start->copy();
                            if ($pEnd->gt($end)) $pEnd = $end->copy();
                            $daysInPeriod = (int) $pStart->diffInDays($pEnd) + 1;
                            $widthPct = ($daysInPeriod / $totalMonthDays) * 100;
                        @endphp
                        <div style="width: {{ $widthPct }}%" class="text-center text-xs text-gray-500 dark:text-gray-400 py-2 border-r border-gray-100 dark:border-gray-700 last:border-r-0 truncate px-1">
                            {{ $period['label'] }}
                        </div>
                    @endforeach
                </div>

                {{-- Today marker --}}
                <div style="position: relative; min-height: 100px;">
                    <div style="position: absolute; top: 0; bottom: 0; left: {{ $todayOffset }}%; width: 2px; background: #f87171; z-index: 10;">
                        <div class="bg-white dark:bg-gray-800 px-1 rounded" style="position: absolute; top: -18px; transform: translateX(-50%); font-size: 9px; color: #ef4444; font-weight: 600; white-space: nowrap;">{{ __('dashboard.today') }}</div>
                    </div>

                    {{-- Project bars --}}
                    <div class="py-2" style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach ($projects as $project)
                            @php
                                $projStart = $project->project_start;
                                $projEnd = $project->planned_go_live;
                                $projDays = max(1, (int) $projStart->diffInDays($projEnd));
                                $leftPct = max(0, round(((int) $start->diffInDays($projStart) / $totalMonthDays) * 100, 2));
                                $widthPct = max(3, round(($projDays / $totalMonthDays) * 100, 2));
                                $barBg = match($project->overall_health) {
                                    'on_track' => '#4ade80',
                                    'at_risk' => '#facc15',
                                    'off_track' => '#f87171',
                                };
                                $textColor = $project->overall_health === 'at_risk' ? '#713f12' : '#ffffff';
                                $hasPhases = $project->phases->isNotEmpty();
                                $healthDotColor = match($project->overall_health) {
                                    'on_track'  => '#16a34a',
                                    'at_risk'   => '#ca8a04',
                                    'off_track' => '#dc2626',
                                    default     => '#6b7280',
                                };
                            @endphp
                            <div style="position: relative; height: 32px;"
                                 :style="{ height: (showPhases && {{ $hasPhases ? 'true' : 'false' }}) ? '48px' : '32px' }">
                                {{-- Main project bar with tooltip --}}
                                <div x-data="{ barOpen: false }"
                                     @mouseenter="barOpen = true"
                                     @mouseleave="barOpen = false"
                                     style="position: absolute; top: 0; left: {{ $leftPct }}%; width: {{ $widthPct }}%; height: 32px;">
                                    <a href="{{ route('projects.edit', $project->id) }}" wire:navigate
                                       style="position: absolute; inset: 0; background: {{ $barBg }}; border-radius: 4px; display: flex; align-items: center; padding: 0 8px; overflow: hidden; text-decoration: none;">
                                        <span style="font-size: 10px; color: {{ $textColor }}; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $project->name }}</span>
                                    </a>
                                    <div x-show="barOpen"
                                         style="position: absolute; bottom: calc(100% + 7px); left: 50%; transform: translateX(-50%); background: #111827; color: #f9fafb; font-size: 11px; padding: 6px 10px; border-radius: 6px; white-space: nowrap; z-index: 300; pointer-events: none; line-height: 1.5; box-shadow: 0 4px 12px rgba(0,0,0,0.4);">
                                        <div style="display: flex; align-items: center; gap: 5px; font-weight: 600;">
                                            <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: {{ $healthDotColor }}; flex-shrink: 0;"></span>
                                            {{ $project->name }}
                                        </div>
                                        <div style="font-size: 10px; opacity: 0.75; margin-top: 2px; padding-left: 12px;">
                                            {{ __('projects.health_' . $project->overall_health) }} · {{ $projStart->format('d.m.Y') }} → {{ $projEnd->format('d.m.Y') }}
                                        </div>
                                        <div style="position: absolute; bottom: -5px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 5px solid #111827;"></div>
                                    </div>
                                </div>

                                {{-- Phase strip: all phases, equal width, colored by status --}}
                                @if ($hasPhases)
                                    @php
                                        $phasesOrdered = $project->phases->sortBy('sort_order')->values();
                                        $phaseCount    = $phasesOrdered->count();
                                        $segW          = round(100 / $phaseCount, 4);
                                    @endphp
                                    <div :style="{ display: showPhases ? 'flex' : 'none' }"
                                         style="position:absolute; top:35px; left:{{ $leftPct }}%; width:{{ $widthPct }}%; height:13px; display:none;">
                                        @foreach ($phasesOrdered as $phase)
                                            @php
                                                [$segBg, $segText, $dotColor] = match($phase->status) {
                                                    'in_progress' => ['#bfdbfe', '#1d4ed8', '#3b82f6'],
                                                    'done'        => ['#bbf7d0', '#15803d', '#22c55e'],
                                                    'blocked'     => ['#fecaca', '#b91c1c', '#ef4444'],
                                                    default       => ['#e5e7eb', '#6b7280', '#9ca3af'],
                                                };
                                                $dateStr     = $phase->completion_date ? $phase->completion_date->format('d.m.Y') : null;
                                                $statusLabel = \App\Models\ProjectPhase::getStatusLabel($phase->status);
                                                $br  = '';
                                                if ($loop->first && $loop->last) $br = 'border-radius:2px;';
                                                elseif ($loop->first)            $br = 'border-radius:2px 0 0 2px;';
                                                elseif ($loop->last)             $br = 'border-radius:0 2px 2px 0;';
                                                $sep = !$loop->last ? 'border-right:1px solid rgba(255,255,255,0.7);' : '';
                                            @endphp
                                            <div x-data="{ open: false }"
                                                 @mouseenter="open = true"
                                                 @mouseleave="open = false"
                                                 style="width:{{ $segW }}%; background:{{ $segBg }}; flex-shrink:0; position:relative; cursor:default; {{ $br }}{{ $sep }}">
                                                {{-- Tooltip (no display:none — x-show manages visibility) --}}
                                                <div x-show="open"
                                                     style="position:absolute; bottom:calc(100% + 7px); left:50%; transform:translateX(-50%); background:#111827; color:#f9fafb; font-size:11px; padding:6px 10px; border-radius:6px; white-space:nowrap; z-index:300; pointer-events:none; line-height:1.5; box-shadow:0 4px 12px rgba(0,0,0,0.4);">
                                                    <div style="display:flex; align-items:center; gap:5px; font-weight:600;">
                                                        <span style="display:inline-block; width:7px; height:7px; border-radius:50%; background:{{ $dotColor }}; flex-shrink:0;"></span>
                                                        P{{ $phase->sort_order }} &middot; {{ $phase->phase_name }}
                                                    </div>
                                                    <div style="font-size:10px; opacity:0.75; margin-top:2px; padding-left:12px;">
                                                        {{ $statusLabel }}{{ $dateStr ? ' · ' . $dateStr : '' }}
                                                    </div>
                                                    {{-- Caret --}}
                                                    <div style="position:absolute; bottom:-5px; left:50%; transform:translateX(-50%); width:0; height:0; border-left:5px solid transparent; border-right:5px solid transparent; border-top:5px solid #111827;"></div>
                                                </div>
                                                {{-- Label --}}
                                                <div style="display:flex; align-items:center; justify-content:center; height:100%; overflow:hidden;">
                                                    <span style="font-size:7px; font-weight:700; color:{{ $segText }}; white-space:nowrap; overflow:hidden; padding:0 2px;">P{{ $phase->sort_order }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
    </div>
</div>

