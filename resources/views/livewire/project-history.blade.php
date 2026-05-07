<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('projects.history_title', ['name' => $project->name]) }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('projects.edit', $project) }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    {{ __('projects.edit_project') }}
                </a>
                <a href="{{ route('projects.index') }}" wire:navigate class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                    &larr; {{ __('projects.back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Snapshot list --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">{{ __('projects.versions_count', ['count' => $snapshots->total()]) }}</h3>
                        <div class="space-y-2">
                            @forelse ($snapshots as $index => $snapshot)
                                <div class="flex items-start gap-2">
                                    <button wire:click="viewSnapshot({{ $snapshot->id }})"
                                            class="flex-1 text-left p-3 rounded-md border transition
                                                {{ $selectedSnapshotId === $snapshot->id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                                                    {{ $snapshot->version ?? 'N/A' }}
                                                </div>
                                                <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5">
                                                    {{ $snapshot->user->name ?? 'System' }}
                                                </div>
                                            </div>
                                            <div class="text-[10px] text-gray-400 dark:text-gray-500">
                                                {{ $snapshot->created_at->format('d.m.Y H:i') }}
                                            </div>
                                        </div>
                                        @if ($snapshot->change_note)
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $snapshot->change_note }}</div>
                                        @endif
                                    </button>
                                    @if ($selectedSnapshotId && $selectedSnapshotId !== $snapshot->id)
                                        <button wire:click="compareSnapshots({{ $snapshot->id }}, {{ $selectedSnapshotId }})"
                                                class="mt-2 px-2 py-1 text-[10px] bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded hover:bg-purple-200 dark:hover:bg-purple-900/50 transition flex-shrink-0"
                                                title="{{ __('projects.compare') }}">
                                            {{ __('projects.compare') }}
                                        </button>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">{{ __('projects.no_history') }}</p>
                            @endforelse
                        </div>
                        <div class="mt-3">
                            {{ $snapshots->links() }}
                        </div>
                    </div>
                </div>

                {{-- Snapshot detail / Diff --}}
                <div class="lg:col-span-2">
                    @if ($showDiff && count($diffResults) > 0)
                        {{-- Diff view --}}
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('projects.diff_title') }}</h3>
                                <button wire:click="closeSnapshot" aria-label="{{ __('projects.aria_close') }}" class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">&times; {{ __('projects.close') }}</button>
                            </div>
                            <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                        <th class="px-3 py-2 text-left font-semibold">{{ __('projects.diff_field') }}</th>
                                        <th class="px-3 py-2 text-left font-semibold text-red-600 dark:text-red-400">{{ __('projects.diff_before') }}</th>
                                        <th class="px-3 py-2 text-left font-semibold text-green-600 dark:text-green-400">{{ __('projects.diff_after') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($diffResults as $diff)
                                        <tr class="border-b border-gray-100 dark:border-gray-700">
                                            <td class="px-3 py-2 font-medium text-gray-700 dark:text-gray-200">{{ $diff['field'] }}</td>
                                            <td class="px-3 py-2 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400">{{ $diff['old'] }}</td>
                                            <td class="px-3 py-2 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400">{{ $diff['new'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                    @elseif ($showDiff && count($diffResults) === 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('projects.diff_title') }}</h3>
                                <button wire:click="closeSnapshot" aria-label="{{ __('projects.aria_close') }}" class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">&times; {{ __('projects.close') }}</button>
                            </div>
                            <div class="text-center py-6 text-gray-400 dark:text-gray-500">
                                <p>{{ __('projects.no_changes') }}</p>
                            </div>
                        </div>
                    @elseif ($selectedSnapshot)
                        <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 rounded-lg shadow-sm p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('projects.snapshot_detail') }}</h3>
                                <button wire:click="closeSnapshot" aria-label="{{ __('projects.aria_close') }}" class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">&times; {{ __('projects.close') }}</button>
                            </div>

                            {{-- Project meta --}}
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $selectedSnapshot['name'] ?? '-' }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('projects.client') }}: {{ $selectedSnapshot['client'] ?? '-' }}</p>
                                    </div>
                                    @php
                                        $snapType = $selectedSnapshot['project_type'] ?? 'new';
                                        $snapBadge = match($snapType) {
                                            'new' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                            'migration' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                                            'cr' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400',
                                            default => 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300',
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium {{ $snapBadge }}">
                                        {{ __('projects.type_' . $snapType) }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3 text-xs">
                                    <div><span class="text-gray-500 dark:text-gray-400">{{ __('projects.team_lead') }}:</span> {{ $selectedSnapshot['team_lead'] ?? '-' }}</div>
                                    <div><span class="text-gray-500 dark:text-gray-400">{{ __('projects.phase') }}:</span> {{ isset($selectedSnapshot['current_phase']) ? \App\Models\Project::phaseLabel($selectedSnapshot['current_phase']) : '-' }}</div>
                                    <div><span class="text-gray-500 dark:text-gray-400">{{ __('projects.overall_health') }}:</span>
                                        @php $h = $selectedSnapshot['overall_health'] ?? 'on_track'; @endphp
                                        {{ match($h) { 'on_track' => '🟢', 'at_risk' => '🟡', 'off_track' => '🔴', default => '' } }}
                                        {{ __('projects.health_' . $h) }}
                                    </div>
                                    <div><span class="text-gray-500 dark:text-gray-400">{{ __('projects.go_live') }}:</span> {{ $selectedSnapshot['planned_go_live'] ?? '-' }}</div>
                                </div>
                            </div>

                            {{-- Phases --}}
                            @if (!empty($selectedSnapshot['phases']))
                                <div class="mb-4">
                                    <h4 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-2">{{ __('projects.phases_title') }}</h4>
                                    <div class="overflow-x-auto">
                                    <table class="w-full text-xs">
                                        <thead>
                                            <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                                <th class="px-2 py-1 text-left">{{ __('projects.phase') }}</th>
                                                <th class="px-2 py-1 text-left">{{ __('projects.status') }}</th>
                                                <th class="px-2 py-1 text-left">{{ __('projects.date') }}</th>
                                                <th class="px-2 py-1 text-left">{{ __('projects.notes') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selectedSnapshot['phases'] as $phase)
                                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                                    <td class="px-2 py-1.5 font-medium">{{ $phase['phase_name'] }}</td>
                                                    <td class="px-2 py-1.5">
                                                        @php $s = $phase['status'] ?? 'pending'; @endphp
                                                        {{ match($s) { 'pending' => '⚪', 'in_progress' => '🔵', 'done' => '✅', 'blocked' => '🔴', default => '' } }}
                                                        {{ __('projects.status_' . $s) }}
                                                    </td>
                                                    <td class="px-2 py-1.5">{{ $phase['completion_date'] ?? '-' }}</td>
                                                    <td class="px-2 py-1.5 text-gray-600 dark:text-gray-400">{{ $phase['notes'] ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            @endif

                            {{-- Estimation --}}
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded p-2 text-center">
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ __('projects.estimated_hours') }}</div>
                                    <div class="text-sm font-bold">{{ $selectedSnapshot['estimated_hours'] ?? '-' }}h</div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded p-2 text-center">
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ __('projects.spent_hours') }}</div>
                                    <div class="text-sm font-bold">{{ $selectedSnapshot['spent_hours'] ?? '-' }}h</div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded p-2 text-center">
                                    <div class="text-[10px] text-gray-500 dark:text-gray-400">{{ __('projects.remaining_hours') }}</div>
                                    <div class="text-sm font-bold">{{ $selectedSnapshot['remaining_hours'] ?? '-' }}h</div>
                                </div>
                            </div>

                            {{-- Risks --}}
                            @if (!empty($selectedSnapshot['risks']))
                                <div class="mb-4">
                                    <h4 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-2">{{ __('projects.risks_title') }}</h4>
                                    @foreach ($selectedSnapshot['risks'] as $risk)
                                        <div class="flex gap-2 mb-1 text-xs">
                                            <span>{{ match($risk['level'] ?? 'medium') { 'low' => '🟢', 'medium' => '🟡', 'high' => '🔴', default => '' } }}</span>
                                            <span class="font-medium">{{ $risk['description'] ?? '' }}</span>
                                            <span class="text-gray-400 dark:text-gray-500">→</span>
                                            <span class="text-gray-600 dark:text-gray-400">{{ $risk['mitigation'] ?? '' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Next steps --}}
                            @if (!empty($selectedSnapshot['next_steps']))
                                <div>
                                    <h4 class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 font-semibold mb-2">{{ __('projects.next_steps_title') }}</h4>
                                    @foreach ($selectedSnapshot['next_steps'] as $step)
                                        <div class="flex gap-2 mb-1 text-xs">
                                            <span>{{ ($step['is_completed'] ?? false) ? '✅' : '⬜' }}</span>
                                            <span class="{{ ($step['is_completed'] ?? false) ? 'line-through text-gray-400' : '' }}">
                                                {{ $step['description'] ?? '' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center text-gray-400 dark:text-gray-500">
                            <p class="text-lg">{{ __('projects.select_version') }}</p>
                            <p class="text-sm mt-1">{{ __('projects.select_version_hint') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
