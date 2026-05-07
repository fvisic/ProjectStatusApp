<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectAlertNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('projects:send-alerts')]
#[Description('Send email alerts for projects with upcoming go-lives, health issues, and budget overruns')]
class SendProjectAlerts extends Command
{
    public function handle(): int
    {
        $count = 0;

        // Go-live in 7 days or less
        $goLiveSoon = Project::whereNotNull('planned_go_live')
            ->whereDate('planned_go_live', '<=', now()->addDays(7))
            ->whereDate('planned_go_live', '>=', now())
            ->where('current_phase', '!=', 'hypercare')
            ->with('creator')
            ->get();

        foreach ($goLiveSoon as $project) {
            $project->creator->notify(new ProjectAlertNotification($project, 'go_live_soon'));
            $this->notifyAdmins($project, 'go_live_soon');
            $count++;
        }

        // Off-track projects
        $offTrack = Project::where('overall_health', 'off_track')
            ->with('creator')
            ->get();

        foreach ($offTrack as $project) {
            $project->creator->notify(new ProjectAlertNotification($project, 'health_changed'));
            $this->notifyAdmins($project, 'health_changed');
            $count++;
        }

        // Budget overruns >15%
        $allWithEstimates = Project::whereNotNull('estimated_hours')
            ->where('estimated_hours', '>', 0)
            ->with('creator')
            ->get();

        foreach ($allWithEstimates as $project) {
            $forecast = ($project->spent_hours ?? 0) + ($project->remaining_hours ?? 0);
            $pct = (($forecast - $project->estimated_hours) / $project->estimated_hours) * 100;
            if ($pct > 15) {
                $project->creator->notify(new ProjectAlertNotification($project, 'budget_overrun'));
                $this->notifyAdmins($project, 'budget_overrun');
                $count++;
            }
        }

        $this->info("Sent $count alert(s).");
        return self::SUCCESS;
    }

    protected function notifyAdmins(Project $project, string $type): void
    {
        User::where(function ($q) {
                $q->where('is_admin', true)->orWhereIn('role', ['admin', 'manager']);
            })
            ->where('id', '!=', $project->created_by)
            ->each(fn ($admin) => $admin->notify(new ProjectAlertNotification($project, $type)));
    }
}
