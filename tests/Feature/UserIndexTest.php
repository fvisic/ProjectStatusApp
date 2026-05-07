<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_users_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(3)->create();

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSeeLivewire('user-index');
    }

    public function test_admin_can_reset_another_users_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['password' => Hash::make('original-password')]);

        $this->actingAs($admin);

        $component = Livewire::test('user-index')
            ->call('resetPassword', $target->id);

        $target->refresh();

        // New password is set and different from original.
        $this->assertFalse(Hash::check('original-password', $target->password));

        // New password is exposed once in the component state.
        $newPassword = $component->get('lastResetPassword');
        $this->assertNotEmpty($newPassword);
        $this->assertTrue(Hash::check($newPassword, $target->password));
    }

    public function test_admin_can_disable_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['is_disabled' => false]);

        $this->actingAs($admin);

        Livewire::test('user-index')->call('toggleDisabled', $target->id);

        $this->assertTrue($target->fresh()->is_disabled);
    }

    public function test_admin_cannot_disable_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_disabled' => false]);
        $this->actingAs($admin);

        Livewire::test('user-index')->call('toggleDisabled', $admin->id);

        $this->assertFalse($admin->fresh()->is_disabled);
    }

    public function test_admin_can_disable_another_users_two_factor(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create([
            'two_factor_secret' => 'SECRET',
            'two_factor_recovery_codes' => ['a', 'b'],
            'two_factor_confirmed_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test('user-index')->call('disableTwoFactor', $target->id);

        $target->refresh();
        $this->assertNull($target->two_factor_secret);
        $this->assertNull($target->two_factor_recovery_codes);
        $this->assertNull($target->two_factor_confirmed_at);
    }

    public function test_admin_can_change_user_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin);

        Livewire::test('user-index')->call('changeRole', $target->id, 'manager');

        $this->assertEquals('manager', $target->fresh()->role);
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Livewire::test('user-index')->call('changeRole', $admin->id, 'user');

        $this->assertEquals('admin', $admin->fresh()->role);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create();

        $this->actingAs($admin);

        Livewire::test('user-index')->call('deleteUser', $target->id);

        $this->assertNull(User::find($target->id));
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Livewire::test('user-index')->call('deleteUser', $admin->id);

        $this->assertNotNull(User::find($admin->id));
    }

    public function test_users_can_be_searched(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);
        User::factory()->create(['name' => 'Alice Anderson', 'email' => 'alice@example.com']);
        User::factory()->create(['name' => 'Bob Baker', 'email' => 'bob@example.com']);

        $this->actingAs($admin);

        Livewire::test('user-index')
            ->set('search', 'alice')
            ->assertSee('Alice Anderson')
            ->assertDontSee('Bob Baker');
    }
}
