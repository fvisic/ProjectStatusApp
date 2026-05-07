<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_start_impersonating_regular_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)
            ->post(route('impersonate.start', $target))
            ->assertRedirect(route('dashboard'));

        $this->assertEquals($target->id, auth()->id());
        $this->assertEquals($admin->id, session('impersonating'));
    }

    public function test_admin_can_stop_impersonation_and_return_to_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)
            ->post(route('impersonate.start', $target));

        $this->post(route('impersonate.stop'))
            ->assertRedirect(route('dashboard'));

        $this->assertEquals($admin->id, auth()->id());
        $this->assertNull(session('impersonating'));
    }

    public function test_non_admin_cannot_impersonate(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $target = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->post(route('impersonate.start', $target))
            ->assertForbidden();

        $this->assertEquals($user->id, auth()->id());
    }

    public function test_manager_cannot_impersonate(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $target = User::factory()->create(['role' => 'user']);

        $this->actingAs($manager)
            ->post(route('impersonate.start', $target))
            ->assertForbidden();
    }

    public function test_admin_cannot_impersonate_another_admin(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin1)
            ->post(route('impersonate.start', $admin2))
            ->assertForbidden();
    }

    public function test_impersonation_cannot_be_chained(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $userA = User::factory()->create(['role' => 'user']);
        $userB = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)
            ->post(route('impersonate.start', $userA));

        // Now acting as $userA, try to impersonate $userB - should fail
        $this->post(route('impersonate.start', $userB))
            ->assertForbidden();
    }

    public function test_guests_cannot_access_impersonation_routes(): void
    {
        $target = User::factory()->create();

        $this->post(route('impersonate.start', $target))
            ->assertRedirect(route('login'));

        $this->post(route('impersonate.stop'))
            ->assertRedirect(route('login'));
    }

    public function test_stop_without_active_impersonation_is_noop(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('impersonate.stop'))
            ->assertRedirect(route('dashboard'));

        $this->assertEquals($admin->id, auth()->id());
    }

    public function test_banner_renders_when_impersonating(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'user', 'name' => 'Impersonatee']);

        // Drive through the actual start route so session + auth both flip
        $this->actingAs($admin)
            ->post(route('impersonate.start', $target))
            ->assertRedirect(route('dashboard'));

        // Follow up as target (simulating subsequent request after controller set session)
        $this->actingAs($target)
            ->withSession(['impersonating' => $admin->id])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(__('impersonation.stop'))
            ->assertSee(route('impersonate.stop'));
    }

    public function test_banner_absent_when_not_impersonating(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('impersonate.stop'));
    }

    public function test_admin_cannot_impersonate_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('impersonate.start', $admin))
            ->assertRedirect();

        // Should not have switched or set session
        $this->assertEquals($admin->id, auth()->id());
        $this->assertNull(session('impersonating'));
    }
}
