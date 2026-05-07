<?php

namespace App\Livewire;

use App\Models\Project;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProjectTimeline extends Component
{
    #[Url]
    public int $zoom = 2; // 1=quarters, 2=months, 3=weeks

    public function zoomIn(): void
    {
        $this->zoom = min(3, $this->zoom + 1);
    }

    public function zoomOut(): void
    {
        $this->zoom = max(1, $this->zoom - 1);
    }

    public function render()
    {
        $query = auth()->user()->isAdminOrManager()
            ? Project::query()
            : Project::where('created_by', auth()->id());

        $projects = $query
            ->whereNotNull('project_start')
            ->whereNotNull('planned_go_live')
            ->orderBy('project_start')
            ->with('phases')
            ->get();

        $minDate = $projects->min('project_start');
        $maxDate = $projects->max('planned_go_live');

        if ($minDate && $maxDate) {
            $totalDays = max(1, (int) $minDate->copy()->startOfMonth()->diffInDays($maxDate->copy()->endOfMonth()));
        } else {
            $totalDays = 1;
        }

        // Build time periods based on zoom level
        $periods = [];
        if ($minDate && $maxDate) {
            $start = $minDate->copy()->startOfMonth();
            $end = $maxDate->copy()->endOfMonth();

            if ($this->zoom === 1) {
                // Quarters
                $cursor = $start->copy()->startOfQuarter();
                while ($cursor->lte($end)) {
                    $qStart = $cursor->copy();
                    $qEnd = $cursor->copy()->endOfQuarter();
                    $periods[] = [
                        'label' => 'Q' . $qStart->quarter . ' ' . $qStart->format('Y'),
                        'start' => $qStart,
                        'end' => $qEnd,
                    ];
                    $cursor->addQuarter();
                }
            } elseif ($this->zoom === 2) {
                // Months
                $cursor = $start->copy();
                while ($cursor->lte($end)) {
                    $mStart = $cursor->copy()->startOfMonth();
                    $mEnd = $cursor->copy()->endOfMonth();
                    $periods[] = [
                        'label' => $cursor->translatedFormat('M Y'),
                        'start' => $mStart,
                        'end' => $mEnd,
                    ];
                    $cursor->addMonth();
                }
            } else {
                // Weeks
                $cursor = $start->copy()->startOfWeek();
                while ($cursor->lte($end)) {
                    $wStart = $cursor->copy();
                    $wEnd = $cursor->copy()->endOfWeek();
                    $periods[] = [
                        'label' => $wStart->format('d.m'),
                        'start' => $wStart,
                        'end' => $wEnd,
                    ];
                    $cursor->addWeek();
                }
            }
        }

        return view('livewire.project-timeline', compact('projects', 'minDate', 'maxDate', 'totalDays', 'periods'));
    }
}
