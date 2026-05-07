<?php

namespace App\Notifications;

use App\Channels\WebhookChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyReportNotification extends Notification
{
    use Queueable;

    public function __construct(protected array $summary) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        if ($notifiable->slack_webhook_url) {
            $channels[] = WebhookChannel::class;
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $s = $this->summary;
        $total = $s['projects']->count();
        $onTrack = $s['healthCounts']['on_track'] ?? 0;
        $atRisk = $s['healthCounts']['at_risk'] ?? 0;
        $offTrack = $s['healthCounts']['off_track'] ?? 0;

        $message = (new MailMessage)
            ->subject('Weekly Portfolio Report - ' . now()->format('d.m.Y'))
            ->greeting('Portfolio Summary')
            ->line("**{$total}** projects total: {$onTrack} On Track, {$atRisk} At Risk, {$offTrack} Off Track")
            ->line("Estimation: {$s['totalSpent']}h spent of {$s['totalEstimated']}h estimated (forecast delta: {$s['overallDelta']}%)");

        if ($s['offTrack']->isNotEmpty()) {
            $names = $s['offTrack']->pluck('name')->join(', ');
            $message->line("**Off Track:** {$names}");
        }

        if ($s['upcomingGoLives']->isNotEmpty()) {
            $goLives = $s['upcomingGoLives']->map(fn ($p) => "{$p->name} ({$p->planned_go_live->format('d.m.Y')})")->join(', ');
            $message->line("**Upcoming Go-Lives (14d):** {$goLives}");
        }

        return $message->action('Open Dashboard', url('/dashboard'));
    }

    public function toWebhook(object $notifiable): array
    {
        $s = $this->summary;
        $total = $s['projects']->count();
        $onTrack = $s['healthCounts']['on_track'] ?? 0;
        $atRisk = $s['healthCounts']['at_risk'] ?? 0;
        $offTrack = $s['healthCounts']['off_track'] ?? 0;

        $text = "**Weekly Portfolio Report - " . now()->format('d.m.Y') . "**\n"
            . "Projects: {$total} total | {$onTrack} On Track | {$atRisk} At Risk | {$offTrack} Off Track\n"
            . "Hours: {$s['totalSpent']}h / {$s['totalEstimated']}h (delta: {$s['overallDelta']}%)";

        if ($s['offTrack']->isNotEmpty()) {
            $text .= "\nOff Track: " . $s['offTrack']->pluck('name')->join(', ');
        }

        return ['text' => $text];
    }

    public function toArray(object $notifiable): array
    {
        $s = $this->summary;
        $total = $s['projects']->count();

        return [
            'type' => 'weekly_report',
            'message' => "Weekly portfolio report: {$total} projects",
        ];
    }
}
