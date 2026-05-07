<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(
        protected int $userId,
        protected bool $isAdmin = false,
        protected string $filterHealth = '',
        protected string $filterType = '',
    ) {}

    public function query()
    {
        $query = $this->isAdmin ? Project::query() : Project::where('created_by', $this->userId);

        return $query
            ->when($this->filterHealth, fn ($q) => $q->where('overall_health', $this->filterHealth))
            ->when($this->filterType, fn ($q) => $q->where('project_type_id', $this->filterType))
            ->with('projectType')
            ->latest();
    }

    public function headings(): array
    {
        return [
            __('projects.project_name'),
            __('projects.client'),
            __('projects.type'),
            __('projects.current_phase'),
            __('projects.overall_health'),
            __('projects.team_lead'),
            __('projects.project_start'),
            __('projects.planned_go_live'),
            __('projects.estimated_hours'),
            __('projects.spent_hours'),
            __('projects.remaining_hours'),
            'Delta %',
            __('projects.version'),
        ];
    }

    public function map($project): array
    {
        $forecast = ($project->spent_hours ?? 0) + ($project->remaining_hours ?? 0);
        $delta = $project->estimated_hours ? round((($forecast - $project->estimated_hours) / $project->estimated_hours) * 100) : 0;

        return [
            $project->name,
            $project->client ?? '',
            $project->projectType?->name ?? '-',
            Project::phaseLabel($project->current_phase),
            __('projects.health_' . $project->overall_health),
            $project->team_lead ?? '',
            $project->project_start?->format('d.m.Y') ?? '',
            $project->planned_go_live?->format('d.m.Y') ?? '',
            $project->estimated_hours ?? 0,
            $project->spent_hours ?? 0,
            $project->remaining_hours ?? 0,
            ($delta > 0 ? '+' : '') . $delta . '%',
            $project->version ?? '',
        ];
    }
}
