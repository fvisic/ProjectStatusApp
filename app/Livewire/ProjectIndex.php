<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\ProjectType;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterHealth = '';
    public string $filterType = '';
    public ?int $editingId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterHealth(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function startInlineEdit(int $id): void
    {
        $this->editingId = $id;
    }

    public function cancelInlineEdit(): void
    {
        $this->editingId = null;
    }

    public function saveInlineEdit(int $id, string $field, string $value): void
    {
        $project = Project::findOrFail($id);
        $this->authorize('update', $project);

        $allowed = [
            'overall_health' => ['on_track', 'at_risk', 'off_track'],
            'current_phase' => Project::$phaseKeys,
            'spent_hours' => null,
        ];

        if (!array_key_exists($field, $allowed)) {
            return;
        }

        if ($field === 'spent_hours') {
            $project->update([$field => max(0, (int) $value), 'updated_by' => auth()->id()]);
        } else {
            if (!in_array($value, $allowed[$field])) {
                return;
            }
            $project->update([$field => $value, 'updated_by' => auth()->id()]);
        }

        $project->createSnapshot(auth()->id(), 'Inline edit');
        $this->editingId = null;
    }

    public function deleteProject(int $id): void
    {
        $project = Project::findOrFail($id);
        $this->authorize('delete', $project);
        $project->delete();
    }

    public function render()
    {
        $query = auth()->user()->isAdminOrManager()
            ? Project::query()
            : Project::where('created_by', auth()->id());

        $projects = $query
            ->when($this->search, fn ($q) => $q->where(fn ($q2) =>
                $q2->where('name', 'like', "%{$this->search}%")
                   ->orWhere('client', 'like', "%{$this->search}%")
            ))
            ->when($this->filterHealth, fn ($q) => $q->where('overall_health', $this->filterHealth))
            ->when($this->filterType, fn ($q) => $q->where('project_type_id', $this->filterType))
            ->with('projectType')
            ->latest()
            ->paginate(10);

        $projectTypes = ProjectType::orderBy('sort_order')->get();

        return view('livewire.project-index', compact('projects', 'projectTypes'));
    }
}
