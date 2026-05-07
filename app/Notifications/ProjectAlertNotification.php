<?php

namespace App\Notifications;

use App\Channels\WebhookChannel;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Project $project,
        protected string $alertType,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        if ($notifiable->slack_webhook_url) {
            $channels[] = WebhookChannel::class;
        }
        return $channels;
    }

    public function toWebhook(object $notifiable): array
    {
        $text = match ($this->alertType) {
            'go_live_soon' => "Project **{$this->project->name}** - Go-Live in less than 7 days! ({$this->project->planned_go_live->format('d.m.Y')})",
            'health_changed' => "Project **{$this->project->name}** - health changed to **{$this->project->overall_health}**",
            'budget_overrun' => "Project **{$this->project->name}** - budget overrun >15% (Est: {$this->project->estimated_hours}h, Forecast: " . (($this->project->spent_hours ?? 0) + ($this->project->remaining_hours ?? 0)) . "h)",
            default => "Alert for project {$this->project->name}",
        };

        return ['text' => $text];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("Project Alert: {$this->project->name}");

        return match ($this->alertType) {
            'go_live_soon' => $message
                ->line("Project **{$this->project->name}** has a Go-Live in less than 7 days!")
                ->line("Planned Go-Live: {$this->project->planned_go_live->format('d.m.Y')}")
                ->action('View Project', url("/projects/{$this->project->id}/edit")),

            'health_changed' => $message
                ->line("Project **{$this->project->name}** health changed to **{$this->project->overall_health}**.")
                ->action('View Project', url("/projects/{$this->project->id}/edit")),

            'budget_overrun' => $message
                ->line("Project **{$this->project->name}** has a budget overrun exceeding 15%.")
                ->line("Estimated: {$this->project->estimated_hours}h, Forecast: " . (($this->project->spent_hours ?? 0) + ($this->project->remaining_hours ?? 0)) . "h")
                ->action('View Project', url("/projects/{$this->project->id}/edit")),

            default => $message->line("Alert for project {$this->project->name}."),
        };
    }

    public function toArray(object $notifiable): array
    {
        $estimated = (int) ($this->project->estimated_hours ?? 0);
        $forecast = (int) ($this->project->spent_hours ?? 0) + (int) ($this->project->remaining_hours ?? 0);
        $pct = $estimated > 0 ? (int) round((($forecast - $estimated) / $estimated) * 100) : 0;

        $days = $this->project->planned_go_live
            ? max(0, (int) now()->startOfDay()->diffInDays($this->project->planned_go_live->startOfDay(), false))
            : null;

        return [
            'type' => $this->alertType,
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'days' => $days,
            'pct' => $pct,
            'health' => $this->project->overall_health,
            'message' => match ($this->alertType) {
                'go_live_soon' => "Project {$this->project->name} - Go-Live in less than 7 days!",
                'health_changed' => "Project {$this->project->name} - health changed to {$this->project->overall_health}",
                'budget_overrun' => "Project {$this->project->name} - budget overrun",
                'off_track' => "Project {$this->project->name} is off track",
                default => "Alert for project {$this->project->name}",
            },
        ];
    }
}
