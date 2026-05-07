<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\ProjectPhase;
use App\Models\ProjectType;
use Livewire\Component;

class ProjectForm extends Component
{
    public ?Project $project = null;
    public bool $canEdit = true;
    public string $newComment = '';

    // Project meta
    public string $name = '';
    public string $client = '';
    public string $team_lead = '';
    public ?string $report_date = null;
    public ?string $project_start = null;
    public ?string $planned_go_live = null;
    public ?int $project_type_id = null;
    public string $current_phase = 'instalacija_analiza';
    public string $overall_health = 'on_track';

    // Estimation
    public ?int $estimated_hours = null;
    public ?int $spent_hours = null;
    public ?int $remaining_hours = null;
    public string $estimation_comment = '';

    // Product notification
    public ?string $product_notification_deadline = null;
    public string $product_notification_duration = '';
    public string $product_notification_description = '';

    // Footer
    public string $filled_by = '';
    public string $reviewed_by = '';
    public string $version = 'v1.0';

    /** Tracks the version as loaded — lets us detect manual edits vs auto-bump territory. */
    public string $originalVersion = 'v1.0';

    // Related data
    public array $phases = [];
    public array $risks = [];
    public array $nextSteps = [];

    public function mount(?int $projectId = null): void
    {
        if ($projectId) {
            $this->project = Project::with(['phases', 'risks', 'nextSteps'])->findOrFail($projectId);
            $this->authorize('view', $this->project);
            $this->canEdit = auth()->user()->can('update', $this->project);

            $this->fillFromProject();
        } else {
            $this->report_date = now()->format('Y-m-d');
            $this->initDefaultPhases();
            $this->addRisk();
            $this->addRisk();
            $this->addNextStep();
            $this->addNextStep();
            $this->addNextStep();
        }
    }

    protected function fillFromProject(): void
    {
        $this->name = $this->project->name;
        $this->client = $this->project->client ?? '';
        $this->team_lead = $this->project->team_lead ?? '';
        $this->report_date = $this->project->report_date?->format('Y-m-d');
        $this->project_start = $this->project->project_start?->format('Y-m-d');
        $this->planned_go_live = $this->project->planned_go_live?->format('Y-m-d');
        $this->project_type_id = $this->project->project_type_id;
        $this->current_phase = $this->project->current_phase;
        $this->overall_health = $this->project->overall_health;
        $this->estimated_hours = $this->project->estimated_hours;
        $this->spent_hours = $this->project->spent_hours;
        $this->remaining_hours = $this->project->remaining_hours;
        $this->estimation_comment = $this->project->estimation_comment ?? '';
        $this->product_notification_deadline = $this->project->product_notification_deadline?->format('Y-m-d');
        $this->product_notification_duration = $this->project->product_notification_duration ?? '';
        $this->product_notification_description = $this->project->product_notification_description ?? '';
        $this->filled_by = $this->project->filled_by ?? '';
        $this->reviewed_by = $this->project->reviewed_by ?? '';
        $this->version = $this->project->version ?? 'v1.0';
        $this->originalVersion = $this->version;

        $this->phases = $this->project->phases->map(fn ($p) => [
            'id' => $p->id,
            'phase_name' => $p->phase_name,
            'key_activities' => $p->key_activities ?? '',
            'client_confirmation' => $p->client_confirmation ?? '',
            'status' => $p->status,
            'completion_date' => $p->completion_date?->format('Y-m-d'),
            'notes' => $p->notes ?? '',
            'sort_order' => $p->sort_order,
        ])->toArray();

        $this->risks = $this->project->risks->map(fn ($r) => [
            'id' => $r->id,
            'description' => $r->description ?? '',
            'level' => $r->level,
            'mitigation' => $r->mitigation ?? '',
        ])->toArray();

        $this->nextSteps = $this->project->nextSteps->map(fn ($s) => [
            'id' => $s->id,
            'description' => $s->description ?? '',
            'is_completed' => $s->is_completed,
        ])->toArray();
    }

    protected function initDefaultPhases(): void
    {
        $this->phases = collect(ProjectPhase::getDefaultPhases())->map(fn ($p) => [
            'id' => null,
            'phase_name' => $p['phase_name'],
            'key_activities' => $p['key_activities'],
            'client_confirmation' => $p['client_confirmation'],
            'status' => 'pending',
            'completion_date' => null,
            'notes' => '',
            'sort_order' => $p['sort_order'],
        ])->toArray();
    }

    public function addPhase(): void
    {
        $this->phases[] = [
            'id' => null,
            'phase_name' => '',
            'key_activities' => '',
            'client_confirmation' => '',
            'status' => 'pending',
            'completion_date' => null,
            'notes' => '',
            'sort_order' => count($this->phases) + 1,
        ];
    }

    public function removePhase(int $index): void
    {
        unset($this->phases[$index]);
        $this->phases = array_values($this->phases);
    }

    /**
     * Reorder phases based on an array of current indexes in the desired new order.
     * Called from the SortableJS onEnd handler after a drag completes.
     */
    public function reorderPhases(array $orderedIndexes): void
    {
        $reordered = [];
        foreach ($orderedIndexes as $index) {
            $index = (int) $index;
            if (isset($this->phases[$index])) {
                $reordered[] = $this->phases[$index];
            }
        }

        if (count($reordered) === count($this->phases)) {
            $this->phases = $reordered;
        }
    }

    public function addRisk(): void
    {
        $this->risks[] = [
            'id' => null,
            'description' => '',
            'level' => 'medium',
            'mitigation' => '',
        ];
    }

    public function removeRisk(int $index): void
    {
        unset($this->risks[$index]);
        $this->risks = array_values($this->risks);
    }

    public function addNextStep(): void
    {
        $this->nextSteps[] = [
            'id' => null,
            'description' => '',
            'is_completed' => false,
        ];
    }

    public function removeNextStep(int $index): void
    {
        unset($this->nextSteps[$index]);
        $this->nextSteps = array_values($this->nextSteps);
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'client' => 'nullable|string|max:255',
            'team_lead' => 'nullable|string|max:255',
            'report_date' => 'nullable|date',
            'project_start' => 'nullable|date',
            'planned_go_live' => 'nullable|date',
            'project_type_id' => 'required|exists:project_types,id',
            'current_phase' => 'required|in:' . implode(',', Project::$phaseKeys),
            'overall_health' => 'required|in:on_track,at_risk,off_track',
            'estimated_hours' => 'nullable|integer|min:0',
            'spent_hours' => 'nullable|integer|min:0',
            'remaining_hours' => 'nullable|integer|min:0',
            'estimation_comment' => 'nullable|string',
            'product_notification_deadline' => 'nullable|date',
            'product_notification_duration' => 'nullable|string|max:255',
            'product_notification_description' => 'nullable|string',
            'filled_by' => 'nullable|string|max:255',
            'reviewed_by' => 'nullable|string|max:255',
            'version' => 'nullable|string|max:50',
        ];
    }

    public function save(): void
    {
        if ($this->project) {
            $this->authorize('update', $this->project);
        }

        $this->validate();

        $isNew = $this->project === null;
        $previousSnapshot = $isNew ? null : $this->project->snapshots()->latest('id')->first();

        $data = [
            'name' => $this->name,
            'client' => $this->client ?: null,
            'team_lead' => $this->team_lead ?: null,
            'report_date' => $this->report_date ?: null,
            'project_start' => $this->project_start ?: null,
            'planned_go_live' => $this->planned_go_live ?: null,
            'project_type_id' => $this->project_type_id,
            'current_phase' => $this->current_phase,
            'overall_health' => $this->overall_health,
            'estimated_hours' => $this->estimated_hours,
            'spent_hours' => $this->spent_hours,
            'remaining_hours' => $this->remaining_hours,
            'estimation_comment' => $this->estimation_comment ?: null,
            'product_notification_deadline' => $this->product_notification_deadline ?: null,
            'product_notification_duration' => $this->product_notification_duration ?: null,
            'product_notification_description' => $this->product_notification_description ?: null,
            'filled_by' => $this->filled_by ?: null,
            'reviewed_by' => $this->reviewed_by ?: null,
            'version' => $this->version ?: 'v1.0',
        ];

        if ($this->project) {
            $this->project->update($data + ['updated_by' => auth()->id()]);
        } else {
            $this->project = Project::create($data + ['created_by' => auth()->id(), 'updated_by' => auth()->id()]);
        }

        // Sync phases
        $this->project->phases()->delete();
        foreach ($this->phases as $i => $phase) {
            $this->project->phases()->create([
                'phase_name' => $phase['phase_name'],
                'key_activities' => $phase['key_activities'] ?: null,
                'client_confirmation' => $phase['client_confirmation'] ?: null,
                'status' => $phase['status'],
                'completion_date' => $phase['completion_date'] ?: null,
                'notes' => $phase['notes'] ?: null,
                'sort_order' => $i + 1,
            ]);
        }

        // Sync risks
        $this->project->risks()->delete();
        foreach ($this->risks as $i => $risk) {
            if ($risk['description'] || $risk['mitigation']) {
                $this->project->risks()->create([
                    'description' => $risk['description'] ?: null,
                    'level' => $risk['level'],
                    'mitigation' => $risk['mitigation'] ?: null,
                    'sort_order' => $i + 1,
                ]);
            }
        }

        // Sync next steps
        $this->project->nextSteps()->delete();
        foreach ($this->nextSteps as $i => $step) {
            if ($step['description']) {
                $this->project->nextSteps()->create([
                    'description' => $step['description'],
                    'is_completed' => $step['is_completed'] ?? false,
                    'sort_order' => $i + 1,
                ]);
            }
        }

        // Determine whether this save introduced any real change vs the prior snapshot.
        // For a brand-new project we always snapshot + bump.
        $hasChanges = true;
        if (!$isNew && $previousSnapshot) {
            $newState = $this->relevantSnapshotState(
                $this->project->fresh()->load(['phases', 'risks', 'nextSteps'])->toArray()
            );
            $oldState = $this->relevantSnapshotState($previousSnapshot->snapshot_data ?? []);
            $hasChanges = $newState !== $oldState;
        }

        if ($hasChanges) {
            // Auto-bump minor version unless the user manually edited the version field.
            if (!$isNew && $this->version === $this->originalVersion) {
                $this->project->version = $this->bumpMinorVersion($this->project->version);
                $this->project->save();
                $this->version = $this->project->version;
                $this->originalVersion = $this->version;
            }

            $this->project->createSnapshot(
                auth()->id(),
                $isNew ? __('projects.snapshot_initial') : __('projects.snapshot_changes')
            );

            session()->flash('message', __('projects.saved_successfully'));
        } else {
            session()->flash('message', __('projects.saved_no_changes'));
        }

        $this->redirect(route('projects.index'), navigate: true);
    }

    /**
     * Extract only the user-meaningful fields from a snapshot/project array,
     * normalised for equality comparison. Excludes timestamps, ids, sort_order,
     * and version itself (so an auto-bump doesn't itself count as a change).
     */
    protected function relevantSnapshotState(array $data): array
    {
        $scalar = [
            'name', 'client', 'team_lead', 'project_type_id',
            'current_phase', 'overall_health',
            'estimated_hours', 'spent_hours', 'remaining_hours',
            'estimation_comment',
            'planned_go_live', 'project_start', 'report_date',
            'product_notification_deadline', 'product_notification_duration',
            'product_notification_description',
            'filled_by', 'reviewed_by',
        ];

        $state = [];
        foreach ($scalar as $field) {
            $val = $data[$field] ?? null;
            // Normalise empty string vs null to the same thing.
            $state[$field] = ($val === '' ? null : $val);
        }

        $state['phases'] = collect($data['phases'] ?? [])->map(fn ($p) => [
            'phase_name' => $p['phase_name'] ?? null,
            'key_activities' => $p['key_activities'] ?? null,
            'client_confirmation' => $p['client_confirmation'] ?? null,
            'status' => $p['status'] ?? null,
            'completion_date' => $p['completion_date'] ?? null,
            'notes' => $p['notes'] ?? null,
        ])->values()->all();

        $state['risks'] = collect($data['risks'] ?? [])->map(fn ($r) => [
            'description' => $r['description'] ?? null,
            'level' => $r['level'] ?? null,
            'mitigation' => $r['mitigation'] ?? null,
        ])->values()->all();

        // snapshot_data uses snake_case; relation on the model is camelCase
        $steps = $data['next_steps'] ?? $data['nextSteps'] ?? [];
        $state['next_steps'] = collect($steps)->map(fn ($s) => [
            'description' => $s['description'] ?? null,
            'is_completed' => (bool) ($s['is_completed'] ?? false),
        ])->values()->all();

        return $state;
    }

    /**
     * Bump the minor (last) component of a vMAJOR.MINOR version string.
     * vN.M → vN.(M+1). Falls back to v1.1 for malformed input.
     */
    protected function bumpMinorVersion(?string $version): string
    {
        $version = trim((string) ($version ?? ''));
        if (preg_match('/^v?(\d+)\.(\d+)$/i', $version, $m)) {
            return 'v' . $m[1] . '.' . ((int) $m[2] + 1);
        }
        return 'v1.1';
    }

    public function addComment(): void
    {
        if (!$this->project || !trim($this->newComment)) return;

        $this->project->comments()->create([
            'user_id' => auth()->id(),
            'body' => trim($this->newComment),
        ]);

        $this->newComment = '';
    }

    public function deleteComment(int $commentId): void
    {
        $comment = ProjectComment::findOrFail($commentId);
        if ($comment->user_id === auth()->id() || auth()->user()->isAdmin()) {
            $comment->delete();
        }
    }

    public function getBurndownDataProperty(): array
    {
        if (!$this->project) {
            return [];
        }

        $snapshots = $this->project->snapshots()
            ->orderBy('created_at')
            ->get();

        $points = [];
        foreach ($snapshots as $snap) {
            $data = $snap->snapshot_data ?? [];
            $points[] = [
                'date' => $snap->created_at->format('d.m'),
                'estimated' => $data['estimated_hours'] ?? 0,
                'spent' => $data['spent_hours'] ?? 0,
                'remaining' => $data['remaining_hours'] ?? 0,
            ];
        }

        // Add current state as last point
        $points[] = [
            'date' => now()->format('d.m'),
            'estimated' => $this->estimated_hours ?? 0,
            'spent' => $this->spent_hours ?? 0,
            'remaining' => $this->remaining_hours ?? 0,
        ];

        return $points;
    }

    public function render()
    {
        $comments = $this->project
            ? $this->project->comments()->with('user')->latest()->get()
            : collect();

        $burndownData = $this->burndownData;
        $canEdit = $this->canEdit;
        $projectTypes = ProjectType::orderBy('sort_order')->get();

        return view('livewire.project-form', compact('comments', 'burndownData', 'canEdit', 'projectTypes'));
    }
}
