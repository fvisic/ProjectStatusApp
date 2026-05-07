<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectAlertNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_clicking_notification_redirects_to_project_edit_when_user_can_update(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $project = Project::factory()->create(['created_by' => $user->id]);

        $user->notify(new ProjectAlertNotification($project, 'off_track'));
        $notification = $user->notifications()->first();

        $this->actingAs($user);

        Livewire::test('notification-center')
            ->call('markAsRead', $notification->id)
            ->assertRedirect(route('projects.edit', $project->id));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_clicking_notification_does_not_redirect_when_user_cannot_update(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $manager = User::factory()->create(['role' => 'manager']);
        $project = Project::factory()->create(['created_by' => $owner->id]);

        // Manager receives an alert about someone else's project (can view, cannot update)
        $manager->notify(new ProjectAlertNotification($project, 'off_track'));
        $notification = $manager->notifications()->first();

        $this->actingAs($manager);

        Livewire::test('notification-center')
            ->call('markAsRead', $notification->id)
            ->assertNoRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_admin_is_redirected_to_edit_for_any_project_notification(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create(['created_by' => $owner->id]);

        $admin->notify(new ProjectAlertNotification($project, 'off_track'));
        $notification = $admin->notifications()->first();

        $this->actingAs($admin);

        Livewire::test('notification-center')
            ->call('markAsRead', $notification->id)
            ->assertRedirect(route('projects.edit', $project->id));
    }

    public function test_notification_without_project_id_does_not_redirect(): void
    {
        $user = User::factory()->create();

        Notification::fake();

        // Manually persist a notification with no project_id in data.
        $id = (string) \Illuminate\Support\Str::uuid();
        \DB::table('notifications')->insert([
            'id' => $id,
            'type' => 'App\\Notifications\\GenericNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode(['message' => 'Hello']),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user);

        Livewire::test('notification-center')
            ->call('markAsRead', $id)
            ->assertNoRedirect();

        $this->assertNotNull($user->notifications()->find($id)->read_at);
    }
}
