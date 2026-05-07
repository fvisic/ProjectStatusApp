<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\ProjectSnapshot;
use App\Models\ProjectType;
use Livewire\Component;

class ProjectHistory extends Component
{
    public Project $project;
    public ?array $selectedSnapshot = null;
    public ?int $selectedSnapshotId = null;
    public ?int $compareSnapshotId = null;
    public array $diffResults = [];
    public bool $showDiff = false;

    public function mount(int $projectId): void
    {
        $this->project = Project::findOrFail($projectId);
        $this->authorize('view', $this->project);
    }

    public function viewSnapshot(int $snapshotId): void
    {
        $snapshot = ProjectSnapshot::where('project_id', $this->project->id)
            ->findOrFail($snapshotId);

        $this->selectedSnapshot = $snapshot->snapshot_data;
        $this->selectedSnapshotId = $snapshotId;
        $this->showDiff = false;
        $this->diffResults = [];
    }

    public function compareSnapshots(int $oldId, int $newId): void
    {
        $oldSnapshot = ProjectSnapshot::where('project_id', $this->project->id)->findOrFail($oldId);
        $newSnapshot = ProjectSnapshot::where('project_id', $this->project->id)->findOrFail($newId);

        $this->compareSnapshotId = $oldId;
        $this->selectedSnapshotId = $newId;
        $this->diffResults = $this->computeDiff($oldSnapshot->snapshot_data, $newSnapshot->snapshot_data);
        $this->showDiff = true;
    }

    protected function computeDiff(array $old, array $new): array
    {
        $diff = [];
        $fieldsToCompare = [
            'name', 'client', 'team_lead', 'project_type_id', 'current_phase',
            'overall_health', 'estimated_hours', 'spent_hours', 'remaining_hours',
            'planned_go_live', 'estimation_comment', 'version',
        ];

        $typeNames = ProjectType::withTrashed()->pluck('name', 'id');

        foreach ($fieldsToCompare as $field) {
            $oldVal = $old[$field] ?? null;
            $newVal = $new[$field] ?? null;
            if ($oldVal !== $newVal) {
                $label = $field;
                if ($field === 'current_phase') {
                    $oldVal = $oldVal ? Project::phaseLabel($oldVal) : '-';
                    $newVal = $newVal ? Project::phaseLabel($newVal) : '-';
                    $label = __('projects.current_phase');
                } elseif ($field === 'overall_health') {
                    $oldVal = $oldVal ? __("projects.health_$oldVal") : '-';
                    $newVal = $newVal ? __("projects.health_$newVal") : '-';
                    $label = __('projects.overall_health');
                } elseif ($field === 'project_type_id') {
                    $oldVal = $oldVal ? ($typeNames[$oldVal] ?? "#$oldVal") : '-';
                    $newVal = $newVal ? ($typeNames[$newVal] ?? "#$newVal") : '-';
                    $label = __('projects.type');
                } else {
                    $label = __("projects.$field") !== "projects.$field" ? __("projects.$field") : $field;
                }

                $diff[] = [
                    'field' => $label,
                    'old' => $oldVal ?? '-',
                    'new' => $newVal ?? '-',
                ];
            }
        }

        // Compare phase statuses
        $oldPhases = collect($old['phases'] ?? []);
        $newPhases = collect($new['phases'] ?? []);
        foreach ($newPhases as $i => $newPhase) {
            $oldPhase = $oldPhases->get($i, []);
            if (($oldPhase['status'] ?? '') !== ($newPhase['status'] ?? '')) {
                $diff[] = [
                    'field' => ($newPhase['phase_name'] ?? "Phase $i") . ' - ' . __('projects.status'),
                    'old' => isset($oldPhase['status']) ? __("projects.status_{$oldPhase['status']}") : '-',
                    'new' => isset($newPhase['status']) ? __("projects.status_{$newPhase['status']}") : '-',
                ];
            }
        }

        return $diff;
    }

    public function closeSnapshot(): void
    {
        $this->selectedSnapshot = null;
        $this->selectedSnapshotId = null;
        $this->compareSnapshotId = null;
        $this->showDiff = false;
        $this->diffResults = [];
    }

    public function render()
    {
        $snapshots = $this->project->snapshots()->with('user')->paginate(20);

        return view('livewire.project-history', compact('snapshots'));
    }
}
