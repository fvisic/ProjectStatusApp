<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\ProjectPhase;
use App\Models\ProjectSnapshot;
use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    protected function projectQuery(): Builder
    {
        return auth()->user()->isAdminOrManager()
            ? Project::query()
            : Project::where('created_by', auth()->id());
    }

    public function getProjectsProperty(): Collection
    {
        return $this->projectQuery()->with('projectType')->get();
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user->isAdminOrManager();
        $projects = $this->projects;
        $totalProjects = $projects->count();

        $healthCounts = $projects->groupBy('overall_health')->map->count();
        $typeCounts = $projects->groupBy('project_type_id')->map->count();
        $projectTypes = ProjectType::orderBy('sort_order')->get();
        $phaseCounts = $projects->groupBy('current_phase')->map->count();

        $totalEstimated = $projects->sum('estimated_hours') ?: 0;
        $totalSpent = $projects->sum('spent_hours') ?: 0;
        $totalRemaining = $projects->sum('remaining_hours') ?: 0;
        $totalForecast = $totalSpent + $totalRemaining;
        $overallDelta = $totalEstimated > 0 ? round((($totalForecast - $totalEstimated) / $totalEstimated) * 100) : 0;

        $alertProjects = $projects->whereIn('overall_health', ['at_risk', 'off_track'])->values();

        $upcomingGoLives = $projects
            ->filter(fn ($p) => $p->planned_go_live && $p->planned_go_live->isFuture() && (int) $p->planned_go_live->diffInDays(now()) <= 30)
            ->sortBy('planned_go_live')
            ->values();

        $overdueGoLives = $projects
            ->filter(fn ($p) => $p->planned_go_live && $p->planned_go_live->isPast() && $p->current_phase !== 'hypercare')
            ->sortBy('planned_go_live')
            ->values();

        $overrunProjects = $projects->filter(function ($p) {
            if (!$p->estimated_hours || $p->estimated_hours == 0) return false;
            $forecast = ($p->spent_hours ?? 0) + ($p->remaining_hours ?? 0);
            $pct = (($forecast - $p->estimated_hours) / $p->estimated_hours) * 100;
            return $pct > 15;
        })->values();

        $snapshotQuery = $isAdmin
            ? ProjectSnapshot::query()
            : ProjectSnapshot::whereHas('project', fn ($q) => $q->where('created_by', $user->id));
        $recentActivity = $snapshotQuery->with(['project', 'user'])->latest()->limit(10)->get();

        $blockedQuery = $isAdmin
            ? ProjectPhase::query()
            : ProjectPhase::whereHas('project', fn ($q) => $q->where('created_by', $user->id));
        $blockedPhases = $blockedQuery->where('status', 'blocked')->with('project')->get();

        // Trend data from snapshots (last 8 weeks)
        $trendData = $this->computeTrends($isAdmin, $user->id);

        return view('livewire.dashboard', compact(
            'totalProjects', 'healthCounts', 'typeCounts', 'projectTypes', 'phaseCounts',
            'totalEstimated', 'totalSpent', 'totalRemaining', 'totalForecast',
            'overallDelta', 'alertProjects', 'upcomingGoLives', 'overdueGoLives',
            'overrunProjects', 'recentActivity', 'blockedPhases', 'trendData',
        ));
    }

    protected function computeTrends(bool $isAdmin, int $userId): array
    {
        $weeks = 8;
        $healthTrend = [];
        $spentTrend = [];

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            $weekStart = now()->subWeeks($i)->startOfWeek();

            $snapshotQuery = $isAdmin
                ? ProjectSnapshot::query()
                : ProjectSnapshot::whereHas('project', fn ($q) => $q->where('created_by', $userId));

            $weekSnapshots = $snapshotQuery
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->latest()
                ->get()
                ->unique('project_id');

            $onTrack = 0;
            $atRisk = 0;
            $offTrack = 0;
            $spent = 0;

            foreach ($weekSnapshots as $snap) {
                $data = $snap->snapshot_data ?? [];
                $health = $data['overall_health'] ?? 'on_track';
                match ($health) {
                    'on_track' => $onTrack++,
                    'at_risk' => $atRisk++,
                    'off_track' => $offTrack++,
                    default => null,
                };
                $spent += $data['spent_hours'] ?? 0;
            }

            $healthTrend[] = ['on_track' => $onTrack, 'at_risk' => $atRisk, 'off_track' => $offTrack];
            $spentTrend[] = $spent;
        }

        return [
            'health' => $healthTrend,
            'spent' => $spentTrend,
        ];
    }
}
