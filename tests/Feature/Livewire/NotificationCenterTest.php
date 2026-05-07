<?php

namespace Tests\Feature\Livewire;

use App\Channels\WebhookChannel;
use App\Livewire\NotificationCenter;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectAlertNotification;
use App\Notifications\WeeklyReportNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_notification_center_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->assertOk()
            ->call('toggleDropdown')
            ->assertSee(__('dashboard.notifications'));
    }

    public function test_unread_count_shows_correctly(): void
    {
        // Create a database notification for the user
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $this->user->notify(new ProjectAlertNotification($project, 'off_track'));

        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->call('toggleDropdown')
            ->assertSet('showDropdown', true)
            ->assertSee('1'); // unread count badge
    }

    public function test_mark_as_read_works(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $this->user->notify(new ProjectAlertNotification($project, 'off_track'));

        $notification = $this->user->unreadNotifications->first();

        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->call('markAsRead', $notification->id);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_mark_all_as_read_works(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $this->user->notify(new ProjectAlertNotification($project, 'off_track'));
        $this->user->notify(new ProjectAlertNotification($project, 'health_changed'));

        $this->assertEquals(2, $this->user->unreadNotifications()->count());

        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->call('markAllAsRead');

        $this->assertEquals(0, $this->user->fresh()->unreadNotifications()->count());
    }

    public function test_database_channel_included_in_project_alert_notification(): void
    {
        $project = Project::factory()->create(['created_by' => $this->user->id]);
        $notification = new ProjectAlertNotification($project, 'off_track');
        $channels = $notification->via($this->user);

        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
    }

    public function test_database_channel_included_in_weekly_report_notification(): void
    {
        $summary = [
            'projects' => collect([]),
            'healthCounts' => [],
            'totalEstimated' => 0,
            'totalSpent' => 0,
            'totalForecast' => 0,
            'overallDelta' => 0,
            'offTrack' => collect([]),
            'upcomingGoLives' => collect([]),
        ];

        $notification = new WeeklyReportNotification($summary);
        $channels = $notification->via($this->user);

        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
    }

    public function test_empty_state_shows_when_no_notifications(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->call('toggleDropdown')
            ->assertSee(__('dashboard.no_notifications'));
    }

    public function test_toggle_dropdown(): void
    {
        Livewire::actingAs($this->user)
            ->test(NotificationCenter::class)
            ->assertSet('showDropdown', false)
            ->call('toggleDropdown')
            ->assertSet('showDropdown', true)
            ->call('toggleDropdown')
            ->assertSet('showDropdown', false);
    }
}
