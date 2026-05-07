<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class WebhookChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $url = $notifiable->slack_webhook_url ?? null;

        if (!$url) {
            return;
        }

        $payload = $notification->toWebhook($notifiable);

        // Slack-compatible format (works with MS Teams incoming webhook too)
        Http::post($url, $payload);
    }
}
