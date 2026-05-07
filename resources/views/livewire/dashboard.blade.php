<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('dashboard.title') }}</h2>
            @if ($totalProjects > 0)
                <a href="{{ route('projects.portfolio') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    {{ __('dashboard.portfolio_pdf') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($totalProjects === 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                    <div class="text-6xl mb-4">📊</div>
                    <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-200 mb-2">{{ __('dashboard.welcome') }}</h3>

                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('dashboard.welcome_hint') }}</p>
                    <a href="{{ route('projects.create') }}" wire:navigate
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition">
                        {{ __('projects.new_project') }}
                    </a>
                </div>
            @else

                {{-- KPI Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                        <div class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-medium">{{ __('dashboard.total_projects') }}</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $totalProjects }}</div>
                        <div class="flex gap-2 mt-2 flex-wrap">
                            @foreach ($projectTypes as $pt)
                                @if (($typeCounts[$pt->id] ?? 0) > 0)
                                    <span class="text-xs px-1.5 py-0.5 rounded border {{ $pt->badgeClass() }}">
                                        {{ $typeCounts[$pt->id] }} {{ $pt->name }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                        <div class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-medium">{{ __('dashboard.health_overview') }}</div>
                        <div class="flex items-end gap-3 mt-2">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $healthCounts['on_track'] ?? 0 }}</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400">🟢 {{ __('projects.health_on_track') }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">{{ $healthCounts['at_risk'] ?? 0 }}</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400">🟡 {{ __('projects.health_at_risk') }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">{{ $healthCounts['off_track'] ?? 0 }}</div>
                                <div class="text-[10px] text-gray-500 dark:text-gray-400">🔴 {{ __('projects.health_off_track') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                        <div class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-medium">{{ __('dashboard.estimation_total') }}</div>
                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ number_format($totalSpent) }}h</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('dashboard.of_estimated', ['hours' => number_format($totalEstimated)]) }}
                        </div>
                        @if ($totalEstimated > 0)
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mt-2">
                                @php $pct = min(100, round(($totalSpent / $totalEstimated) * 100)); @endphp
                                <div class="h-2 rounded-full {{ $pct > 100 ? 'bg-red-500' : ($pct > 80 ? 'bg-yellow-500' : 'bg-blue-500') }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                        <div class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-medium">{{ __('dashboard.forecast_delta') }}</div>
                        <div class="text-3xl font-bold mt-1 {{ $overallDelta <= 0 ? 'text-green-600' : ($overallDelta <= 15 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $overallDelta > 0 ? '+' : '' }}{{ $overallDelta }}%
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('dashboard.forecast_vs_est', ['forecast' => number_format($totalForecast), 'estimated' => number_format($totalEstimated)]) }}
                        </div>
                    </div>
                </div>

                {{-- Trend Charts (Chart.js) --}}
                @php
                    $weekLabels = [];
                    for ($i = count($trendData['health']) - 1; $i >= 0; $i--) {
                        $weekLabels[] = now()->subWeeks($i)->format('d.m');
                    }
                    $healthOn = array_map(fn ($w) => $w['on_track'], $trendData['health']);
                    $healthAt = array_map(fn ($w) => $w['at_risk'], $trendData['health']);
                    $healthOff = array_map(fn ($w) => $w['off_track'], $trendData['health']);
                @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                        <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-3">{{ __('dashboard.health_trend') }}</h3>
                        <div wire:ignore class="h-40"
                             x-data="healthTrendChart({
                                 labels: @js($weekLabels),
                                 onTrack: @js($healthOn),
                                 atRisk: @js($healthAt),
                                 offTrack: @js($healthOff),
                             })" x-init="render()">
                            <canvas x-ref="canvas"></canvas>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                        <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-3">{{ __('dashboard.spent_trend') }}</h3>
                        <div wire:ignore class="h-40"
                             x-data="spentTrendChart({
                                 labels: @js($weekLabels),
                                 spent: @js(array_values($trendData['spent'])),
                             })" x-init="render()">
                            <canvas x-ref="canvas"></canvas>
                        </div>
                    </div>
                </div>

                <script>
                    if (typeof window.healthTrendChart === 'undefined') {
                        window.healthTrendChart = function (data) {
                            return {
                                chart: null,
                                render() {
                                    if (this.chart) this.chart.destroy();
                                    const ctx = this.$refs.canvas;
                                    this.chart = new window.Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: data.labels,
                                            datasets: [
                                                { label: 'On Track', data: data.onTrack, backgroundColor: '#22c55e' },
                                                { label: 'At Risk', data: data.atRisk, backgroundColor: '#eab308' },
                                                { label: 'Off Track', data: data.offTrack, backgroundColor: '#ef4444' },
                                            ],
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { display: false } },
                                            scales: {
                                                x: { stacked: true, grid: { display: false }, ticks: { font: { size: 10 } } },
                                                y: { stacked: true, beginAtZero: true, ticks: { precision: 0, font: { size: 10 } } },
                                            },
                                        },
                                    });
                                },
                            };
                        };
                    }
                    if (typeof window.spentTrendChart === 'undefined') {
                        window.spentTrendChart = function (data) {
                            return {
                                chart: null,
                                render() {
                                    if (this.chart) this.chart.destroy();
                                    const ctx = this.$refs.canvas;
                                    this.chart = new window.Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: data.labels,
                                            datasets: [{
                                                label: 'Spent hours',
                                                data: data.spent,
                                                borderColor: '#3b82f6',
                                                backgroundColor: 'rgba(59, 130, 246, 0.15)',
                                                fill: true,
                                                tension: 0.3,
                                                pointRadius: 3,
                                            }],
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { display: false } },
                                            scales: {
                                                x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                                                y: { beginAtZero: true, ticks: { font: { size: 10 } } },
                                            },
                                        },
                                    });
                                },
                            };
                        };
                    }
                </script>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    {{-- Phase distribution (Doughnut chart) --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                        <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-4">{{ __('dashboard.phase_distribution') }}</h3>
                        @php
                            $phaseChartLabels = [];
                            $phaseChartData = [];
                            foreach (\App\Models\Project::getPhaseLabels() as $key => $label) {
                                $phaseChartLabels[] = $label;
                                $phaseChartData[] = $phaseCounts[$key] ?? 0;
                            }
                        @endphp
                        <div wire:ignore class="h-64"
                             x-data="phaseDistributionChart({
                                 labels: @js($phaseChartLabels),
                                 data: @js($phaseChartData),
                             })" x-init="render()">
                            <canvas x-ref="canvas"></canvas>
                        </div>
                        <script>
                            if (typeof window.phaseDistributionChart === 'undefined') {
                                window.phaseDistributionChart = function (data) {
                                    return {
                                        chart: null,
                                        render() {
                                            if (this.chart) this.chart.destroy();
                                            const ctx = this.$refs.canvas;
                                            const colors = ['#60a5fa', '#a78bfa', '#fb923c', '#34d399', '#fbbf24', '#f472b6', '#94a3b8'];
                                            this.chart = new window.Chart(ctx, {
                                                type: 'doughnut',
                                                data: {
                                                    labels: data.labels,
                                                    datasets: [{
                                                        data: data.data,
                                                        backgroundColor: colors,
                                                        borderWidth: 0,
                                                    }],
                                                },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false,
                                                    plugins: {
                                                        legend: {
                                                            position: 'right',
                                                            labels: { font: { size: 10 }, boxWidth: 10, padding: 6 },
                                                        },
                                                    },
                                                },
                                            });
                                        },
                                    };
                                };
                            }
                        </script>
                    </div>

                    {{-- Alerts --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                        <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-4">
                            ⚠️ {{ __('dashboard.attention_needed') }}
                            @if ($alertProjects->count() > 0)
                                <span class="ml-1 px-1.5 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-full text-[10px]">{{ $alertProjects->count() }}</span>
                            @endif
                        </h3>

                        @if ($alertProjects->isEmpty() && $overdueGoLives->isEmpty() && $overrunProjects->isEmpty() && $blockedPhases->isEmpty())
                            <div class="text-center py-6 text-gray-400 dark:text-gray-500">
                                <div class="text-3xl mb-2">✅</div>
                                <p class="text-sm">{{ __('dashboard.all_clear') }}</p>
                            </div>
                        @else
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach ($alertProjects as $p)
                                    <a href="{{ route('projects.edit', $p->id) }}" wire:navigate
                                       class="block p-2 rounded border border-{{ $p->overall_health === 'off_track' ? 'red' : 'yellow' }}-200 dark:border-{{ $p->overall_health === 'off_track' ? 'red' : 'yellow' }}-700 bg-{{ $p->overall_health === 'off_track' ? 'red' : 'yellow' }}-50 dark:bg-{{ $p->overall_health === 'off_track' ? 'red' : 'yellow' }}-900/30 hover:shadow-sm transition">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $p->name }}</span>
                                            <span class="text-[10px] text-gray-700 dark:text-gray-300">{{ $p->overall_health === 'off_track' ? '🔴' : '🟡' }}</span>
                                        </div>
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ $p->client ?? '' }} · {{ \App\Models\Project::phaseLabel($p->current_phase) }}</div>
                                    </a>
                                @endforeach

                                @foreach ($overdueGoLives as $p)
                                    <a href="{{ route('projects.edit', $p->id) }}" wire:navigate
                                       class="block p-2 rounded border border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-900/30 hover:shadow-sm transition">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $p->name }}</span>
                                            <span class="text-[10px] text-red-600 dark:text-red-400 font-semibold">{{ __('dashboard.overdue') }}</span>
                                        </div>
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ __('dashboard.go_live_was', ['date' => $p->planned_go_live->format('d.m.Y'), 'ago' => $p->planned_go_live->diffForHumans()]) }}</div>
                                    </a>
                                @endforeach

                                @foreach ($overrunProjects as $p)
                                    @php
                                        $forecast = ($p->spent_hours ?? 0) + ($p->remaining_hours ?? 0);
                                        $delta = $forecast - $p->estimated_hours;
                                        $pct = round(($delta / $p->estimated_hours) * 100);
                                    @endphp
                                    <a href="{{ route('projects.edit', $p->id) }}" wire:navigate
                                       class="block p-2 rounded border border-orange-200 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/30 hover:shadow-sm transition">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $p->name }}</span>
                                            <span class="text-[10px] text-orange-700 dark:text-orange-400 font-semibold">+{{ $pct }}%</span>
                                        </div>
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ __('dashboard.overrun_label', ['delta' => $delta]) }}</div>
                                    </a>
                                @endforeach

                                @foreach ($blockedPhases as $phase)
                                    <a href="{{ route('projects.edit', $phase->project->id) }}" wire:navigate
                                       class="block p-2 rounded border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 hover:shadow-sm transition">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $phase->project->name }}</span>
                                            <span class="text-[10px] text-gray-700 dark:text-gray-300">🔴 {{ __('projects.status_blocked') }}</span>
                                        </div>
                                        <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ __('projects.phase') }}: {{ $phase->phase_name }}</div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Upcoming Go-Lives --}}
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                        <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-4">🚀 {{ __('dashboard.upcoming_go_lives') }}</h3>
                        @if ($upcomingGoLives->isEmpty())
                            <div class="text-center py-6 text-gray-400 dark:text-gray-500">
                                <div class="text-3xl mb-2">📅</div>
                                <p class="text-sm">{{ __('dashboard.no_upcoming_go_lives') }}</p>
                            </div>
                        @else
                            <div class="space-y-2 max-h-64 overflow-y-auto">
                                @foreach ($upcomingGoLives as $p)
                                    <a href="{{ route('projects.edit', $p->id) }}" wire:navigate
                                       class="block p-3 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $p->name }}</span>
                                            @php $daysLeft = (int) now()->diffInDays($p->planned_go_live); @endphp
                                            <span class="text-xs font-bold {{ $daysLeft <= 7 ? 'text-red-600' : ($daysLeft <= 14 ? 'text-yellow-600' : 'text-green-600') }}">
                                                {{ $daysLeft }}d
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400">{{ $p->client ?? '' }}</span>
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400">{{ $p->planned_go_live->format('d.m.Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 mt-1.5">
                                            @php
                                                $healthIcon = match($p->overall_health) {
                                                    'on_track' => '🟢',
                                                    'at_risk' => '🟡',
                                                    'off_track' => '🔴',
                                                };
                                            @endphp
                                            <span class="text-[10px] text-gray-700 dark:text-gray-300">{{ $healthIcon }} {{ __('projects.health_' . $p->overall_health) }}</span>
                                            <span class="text-gray-300 dark:text-gray-600 mx-1">·</span>
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400">{{ \App\Models\Project::phaseLabel($p->current_phase) }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Bottom section --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                        <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-4">💰 {{ __('dashboard.estimation_per_project') }}</h3>
                        <div class="space-y-3 max-h-80 overflow-y-auto">
                            @foreach ($this->projects->sortByDesc(fn ($p) => ($p->spent_hours ?? 0) + ($p->remaining_hours ?? 0) - ($p->estimated_hours ?? 0)) as $p)
                                @if ($p->estimated_hours)
                                    @php
                                        $est = $p->estimated_hours;
                                        $spent = $p->spent_hours ?? 0;
                                        $rem = $p->remaining_hours ?? 0;
                                        $forecast = $spent + $rem;
                                        $delta = $forecast - $est;
                                        $pct = $est > 0 ? round(($delta / $est) * 100) : 0;
                                        $spentPct = $est > 0 ? min(100, round(($spent / $est) * 100)) : 0;
                                    @endphp
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <a href="{{ route('projects.edit', $p->id) }}" wire:navigate class="text-xs font-medium text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 truncate max-w-[60%]">
                                                {{ $p->name }}
                                            </a>
                                            <span class="text-xs font-semibold {{ $delta <= 0 ? 'text-green-600' : ($pct <= 15 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $spent }}h / {{ $est }}h
                                                @if ($delta != 0)
                                                    ({{ $delta > 0 ? '+' : '' }}{{ $pct }}%)
                                                @endif
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-100 dark:bg-gray-600 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $spentPct > 100 ? 'bg-red-500' : ($spentPct > 80 ? 'bg-yellow-500' : 'bg-blue-500') }}"
                                                 style="width: {{ min(100, $spentPct) }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5">
                        <h3 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-4">🕐 {{ __('dashboard.recent_activity') }}</h3>
                        @if ($recentActivity->isEmpty())
                            <div class="text-center py-6 text-gray-400 dark:text-gray-500">
                                <p class="text-sm">{{ __('dashboard.no_recent_activity') }}</p>
                            </div>
                        @else
                            <div class="space-y-0 max-h-80 overflow-y-auto">
                                @foreach ($recentActivity as $snap)
                                    <div class="flex gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ mb_substr($snap->user->name ?? '?', 0, 1) }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start">
                                                <a href="{{ route('projects.history', $snap->project->id) }}" wire:navigate
                                                   class="text-xs font-medium text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 truncate block">
                                                    {{ $snap->project->name }}
                                                </a>
                                                <span class="text-[10px] text-gray-400 dark:text-gray-500 flex-shrink-0 ml-2">{{ $snap->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="text-[10px] text-gray-500 dark:text-gray-400">
                                                {{ $snap->user->name ?? 'System' }}
                                                · {{ $snap->version ?? '' }}
                                                @if ($snap->change_note)
                                                    · {{ $snap->change_note }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            @endif
        </div>
    </div>
</div>
