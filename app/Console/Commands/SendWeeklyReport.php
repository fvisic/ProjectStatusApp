<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\User;
use App\Notifications\WeeklyReportNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('projects:weekly-report')]
#[Description('Send weekly portfolio summary email to all admins')]
class SendWeeklyReport extends Command
{
    public function handle(): int
    {
        $projects = Project::with(['risks'])->get();

        if ($projects->isEmpty()) {
            $this->info('No projects found - skipping.');
            return self::SUCCESS;
        }

        $healthCounts = $projects->groupBy('overall_health')->map->count();
        $totalEstimated = $projects->sum('estimated_hours') ?: 0;
        $totalSpent = $projects->sum('spent_hours') ?: 0;
        $totalRemaining = $projects->sum('remaining_hours') ?: 0;
        $totalForecast = $totalSpent + $totalRemaining;
        $overallDelta = $totalEstimated > 0
            ? round((($totalForecast - $totalEstimated) / $totalEstimated) * 100)
            : 0;

        $offTrack = $projects->where('overall_health', 'off_track');
        $upcomingGoLives = $projects->filter(
            fn ($p) => $p->planned_go_live
                && $p->planned_go_live->isFuture()
                && (int) $p->planned_go_live->diffInDays(now()) <= 14
        );

        $summary = compact(
            'projects', 'healthCounts', 'totalEstimated', 'totalSpent',
            'totalForecast', 'overallDelta', 'offTrack', 'upcomingGoLives',
        );

        $admins = User::where('is_admin', true)
            ->orWhereIn('role', ['admin', 'manager'])
            ->get();
        $count = 0;

        foreach ($admins as $admin) {
            $admin->notify(new WeeklyReportNotification($summary));
            $count++;
        }

        $this->info("Sent weekly report to $count admin(s).");
        return self::SUCCESS;
    }
}
