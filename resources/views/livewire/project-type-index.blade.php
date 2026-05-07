<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('project_types.title') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
                <div class="p-4 rounded-md bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-sm text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Form --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                    {{ $editingId ? __('project_types.edit') : __('project_types.add') }}
                </h3>

                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input
                            type="text"
                            wire:model.live="name"
                            placeholder="{{ __('project_types.name_placeholder') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        />
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Color picker --}}
                    <div class="flex items-start gap-1.5 flex-wrap">
                        @foreach(\App\Models\ProjectType::$colors as $key => $def)
                            <label class="cursor-pointer" title="{{ $key }}">
                                <input type="radio" wire:model.live="color" value="{{ $key }}" class="sr-only peer" />
                                <span class="inline-block w-7 h-7 rounded-full {{ $def['swatch'] }} ring-2 ring-transparent peer-checked:ring-offset-2 peer-checked:ring-gray-500 dark:peer-checked:ring-gray-300 transition"></span>
                            </label>
                        @endforeach
                        @error('color') <p class="mt-1 text-xs text-red-500 w-full">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button wire:click="save" type="button"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition">
                            {{ $editingId ? __('project_types.update') : __('project_types.create') }}
                        </button>
                        @if($editingId)
                            <button wire:click="cancelEdit" type="button"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                {{ __('project_types.cancel') }}
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Preview --}}
                <div class="mt-3 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('project_types.preview') }}:
                    @php $previewClass = \App\Models\ProjectType::$colors[$color]['badge'] ?? ''; @endphp
                    <span class="inline-block px-3 py-0.5 rounded-full text-xs font-semibold border {{ $previewClass }}">
                        {{ $name ?: __('project_types.name_placeholder') }}
                    </span>
                </div>
            </div>

            {{-- List --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-6 py-3 border-b border-gray-100 dark:border-gray-700">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('project_types.list') }}</span>
                    <label class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 cursor-pointer">
                        <input type="checkbox" wire:model.live="showDeleted" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600" />
                        {{ __('project_types.show_deleted') }}
                    </label>
                </div>

                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($types as $type)
                        <li wire:key="type-{{ $type->id }}" class="flex items-center justify-between px-6 py-3 {{ $type->trashed() ? 'opacity-50' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="inline-block px-3 py-0.5 rounded-full text-xs font-semibold border {{ $type->badgeClass() }}">
                                    {{ $type->name }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $type->projects_count }} {{ __('project_types.projects_count') }}
                                </span>
                                @if($type->trashed())
                                    <span class="text-xs text-red-500 dark:text-red-400">{{ __('project_types.deleted') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                @if($type->trashed())
                                    <button wire:click="restore({{ $type->id }})" type="button"
                                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ __('project_types.restore') }}
                                    </button>
                                @else
                                    <button wire:click="edit({{ $type->id }})" type="button"
                                        class="p-1.5 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="delete({{ $type->id }})"
                                        wire:confirm="{{ __('project_types.confirm_delete', ['name' => $type->name]) }}"
                                        type="button"
                                        class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                    </button>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('project_types.empty') }}
                        </li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>
</div>
