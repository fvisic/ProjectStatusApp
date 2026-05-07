<?php

namespace App\Livewire;

use App\Models\ProjectType;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\Component;

class ProjectTypeIndex extends Component
{
    public string $name = '';
    public string $color = 'blue';
    public ?int $editingId = null;
    public bool $showDeleted = false;

    public function mount(): void
    {
        if (! auth()->user()?->isAdmin()) {
            throw new AuthorizationException();
        }
    }

    public function save(): void
    {
        $this->validate([
            'name'  => 'required|string|max:100',
            'color' => 'required|in:' . implode(',', array_keys(ProjectType::$colors)),
        ]);

        if ($this->editingId) {
            ProjectType::findOrFail($this->editingId)->update([
                'name'  => $this->name,
                'color' => $this->color,
            ]);
        } else {
            ProjectType::create([
                'name'       => $this->name,
                'color'      => $this->color,
                'sort_order' => ProjectType::max('sort_order') + 1,
            ]);
        }

        $this->reset(['name', 'color', 'editingId']);
        $this->color = 'blue';
    }

    public function edit(int $id): void
    {
        $type = ProjectType::findOrFail($id);
        $this->editingId = $id;
        $this->name      = $type->name;
        $this->color     = $type->color;
    }

    public function cancelEdit(): void
    {
        $this->reset(['name', 'editingId']);
        $this->color = 'blue';
    }

    public function delete(int $id): void
    {
        $type = ProjectType::findOrFail($id);

        if ($type->projects()->count() > 0) {
            session()->flash('error', __('project_types.delete_in_use'));
            return;
        }

        $type->delete();
    }

    public function restore(int $id): void
    {
        ProjectType::withTrashed()->findOrFail($id)->restore();
    }

    public function render()
    {
        $types = ProjectType::withTrashed()
            ->when(! $this->showDeleted, fn ($q) => $q->whereNull('deleted_at'))
            ->orderBy('sort_order')
            ->withCount('projects')
            ->get();

        return view('livewire.project-type-index', compact('types'))
            ->title(__('project_types.title'));
    }
}
