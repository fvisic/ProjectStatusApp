<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('projects.title_kanban') }}</h2>
    </x-slot>

    <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm p-3">
        <div class="flex items-center gap-1">
            <a href="{{ route('projects.index') }}" wire:navigate
               class="px-3 py-1.5 text-xs font-medium rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                {{ __('projects.view_list') }}
            </a>
            <a href="{{ route('projects.kanban') }}"
               class="px-3 py-1.5 text-xs font-semibold rounded-md bg-blue-600 text-white">
                {{ __('projects.view_kanban') }}
            </a>
            <a href="{{ route('projects.timeline') }}" wire:navigate
               class="px-3 py-1.5 text-xs font-medium rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                {{ __('projects.view_timeline') }}
            </a>
        </div>
        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-2 px-1">{{ __('projects.kanban_drag_hint') }}</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4" wire:ignore>
        @foreach (['on_track' => ['bg-green-50 dark:bg-green-900/30', 'border-green-300 dark:border-green-700', 'text-green-800 dark:text-green-400', '🟢'], 'at_risk' => ['bg-yellow-50 dark:bg-yellow-900/30', 'border-yellow-300 dark:border-yellow-700', 'text-yellow-800 dark:text-yellow-400', '🟡'], 'off_track' => ['bg-red-50 dark:bg-red-900/30', 'border-red-300 dark:border-red-700', 'text-red-800 dark:text-red-400', '🔴']] as $health => [$bg, $border, $text, $icon])
            <div class="{{ $bg }} rounded-lg border {{ $border }} p-4">
                <h3 class="text-sm font-semibold {{ $text }} mb-3 flex items-center gap-2">
                    <span>{{ $icon }}</span>
                    {{ __('projects.health_' . $health) }}
                    <span class="ml-auto bg-white/60 dark:bg-gray-800/60 px-2 py-0.5 rounded-full text-xs" data-count="{{ $health }}">{{ $columns[$health]->count() }}</span>
                </h3>
                <div class="space-y-2 min-h-[60px] kanban-column"
                     data-health="{{ $health }}">
                    @forelse ($columns[$health] as $project)
                        <div class="bg-white dark:bg-gray-800 rounded-md shadow-sm p-3 border border-gray-100 dark:border-gray-600 hover:shadow-md transition cursor-grab active:cursor-grabbing kanban-card"
                             data-project-id="{{ $project->id }}">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $project->name }}</span>
                                @if ($project->projectType)
                                    <span class="text-[9px] px-1.5 py-0.5 rounded-full border {{ $project->projectType->badgeClass() }} flex-shrink-0 ml-1">
                                        {{ $project->projectType->name }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $project->client ?? '-' }}</div>
                            <div class="flex justify-between items-center mt-2 text-[10px] text-gray-400 dark:text-gray-500">
                                <span>{{ \App\Models\Project::phaseLabel($project->current_phase) }}</span>
                                @if ($project->planned_go_live)
                                    <span class="{{ $project->planned_go_live->isPast() ? 'text-red-500 font-semibold' : '' }}">
                                        {{ $project->planned_go_live->format('d.m.Y') }}
                                    </span>
                                @endif
                            </div>
                            @if ($project->estimated_hours)
                                @php
                                    $spent = $project->spent_hours ?? 0;
                                    $pct = min(100, round(($spent / $project->estimated_hours) * 100));
                                @endphp
                                <div class="w-full bg-gray-100 dark:bg-gray-600 rounded-full h-1.5 mt-2">
                                    <div class="h-1.5 rounded-full {{ $pct > 100 ? 'bg-red-400' : ($pct > 80 ? 'bg-yellow-400' : 'bg-blue-400') }}"
                                         style="width: {{ min(100, $pct) }}%"></div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-6 text-sm text-gray-400 dark:text-gray-500 kanban-empty">-</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
    </div>
    </div>
</div>

@script
<script>
    (function initKanban() {
        if (typeof window.Sortable === 'undefined') {
            setTimeout(initKanban, 100);
            return;
        }

        $wire.el.querySelectorAll('.kanban-column').forEach(function(column) {
            if (column._sortableInstance) return;

            column._sortableInstance = window.Sortable.create(column, {
                group: 'kanban',
                animation: 150,
                ghostClass: 'opacity-30',
                dragClass: 'shadow-lg',
                draggable: '.kanban-card',
                onStart: function () {
                    document.querySelectorAll('.kanban-empty').forEach(function(el) { el.style.display = 'none'; });
                },
                onEnd: function (evt) {
                    var projectId = parseInt(evt.item.dataset.projectId);
                    var newHealth = evt.to.dataset.health;
                    if (projectId && newHealth) {
                        $wire.updateHealth(projectId, newHealth);

                        $wire.el.querySelectorAll('.kanban-column').forEach(function(col) {
                            var health = col.dataset.health;
                            var count = col.querySelectorAll('.kanban-card').length;
                            var badge = $wire.el.querySelector('[data-count="' + health + '"]');
                            if (badge) badge.textContent = count;
                        });
                    }
                }
            });
        });
    })();
</script>
@endscript
