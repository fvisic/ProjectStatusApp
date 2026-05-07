<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Notifications\ProjectAlertNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

#[Signature('notifications:backfill-data {--dry-run : Show what would change without writing}')]
#[Description('Enrich existing ProjectAlertNotification rows with missing days/pct/health fields.')]
class BackfillNotificationData extends Command
{
    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        $rows = DatabaseNotification::where('type', ProjectAlertNotification::class)->get();

        if ($rows->isEmpty()) {
            $this->info('No ProjectAlertNotification rows found.');
            return self::SUCCESS;
        }

        $updated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $data = $row->data ?? [];
            $projectId = $data['project_id'] ?? null;

            if (! $projectId) {
                $skipped++;
                continue;
            }

            $project = Project::withTrashed()->find($projectId);
            if (! $project) {
                $skipped++;
                continue;
            }

            $estimated = (int) ($project->estimated_hours ?? 0);
            $forecast = (int) ($project->spent_hours ?? 0) + (int) ($project->remaining_hours ?? 0);
            $pct = $estimated > 0 ? (int) round((($forecast - $estimated) / $estimated) * 100) : 0;

            // Compute days relative to the notification's creation date — more accurate
            // for historical alerts than "now".
            $days = $project->planned_go_live
                ? max(0, (int) $row->created_at->startOfDay()->diffInDays($project->planned_go_live->startOfDay(), false))
                : null;

            $newData = array_merge($data, [
                'days' => $data['days'] ?? $days,
                'pct' => $data['pct'] ?? $pct,
                'health' => $data['health'] ?? $project->overall_health,
            ]);

            if ($newData == $data) {
                $skipped++;
                continue;
            }

            if (! $dry) {
                $row->data = $newData;
                $row->save();
            }

            $updated++;
        }

        $this->info(($dry ? '[dry-run] ' : '') . "Updated: {$updated}, skipped: {$skipped}, total: {$rows->count()}");

        return self::SUCCESS;
    }
}
