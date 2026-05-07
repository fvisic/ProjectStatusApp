<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;

class ProjectKanban extends Component
{
    public function updateHealth(int $projectId, string $health): void
    {
        if (!in_array($health, ['on_track', 'at_risk', 'off_track'])) {
            return;
        }

        $project = Project::findOrFail($projectId);
        $this->authorize('update', $project);

        if ($project->overall_health === $health) {
            return;
        }

        $project->update(['overall_health' => $health, 'updated_by' => auth()->id()]);
        $project->createSnapshot(auth()->id(), 'Kanban drag & drop');
    }

    public function render()
    {
        $query = auth()->user()->isAdminOrManager()
            ? Project::query()
            : Project::where('created_by', auth()->id());

        $projects = $query->with('projectType')->get();

        $columns = [
            'on_track' => $projects->where('overall_health', 'on_track')->values(),
            'at_risk' => $projects->where('overall_health', 'at_risk')->values(),
            'off_track' => $projects->where('overall_health', 'off_track')->values(),
        ];

        return view('livewire.project-kanban', compact('columns'));
    }
}
