<?php

namespace Tests\Feature;

use App\Livewire\UserIndex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laragear\WebAuthn\Models\WebAuthnCredential;
use Livewire\Livewire;
use Tests\TestCase;

class WebauthnTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: create a fake stored credential for the given user. Skips the
     * full attestation ceremony — useful for testing listing/deletion logic
     * without simulating an authenticator.
     */
    protected function fakeCredentialFor(User $user, ?string $alias = null): WebAuthnCredential
    {
        return $user->webAuthnCredentials()->forceCreate([
            'id' => 'test-credential-' . bin2hex(random_bytes(8)),
            'user_id' => '00000000-0000-0000-0000-000000000000',
            'alias' => $alias,
            'counter' => 0,
            'rp_id' => 'localhost',
            'origin' => 'http://localhost',
            'transports' => ['internal'],
            'aaguid' => '00000000-0000-0000-0000-000000000000',
            'public_key' => 'fake-encrypted-public-key',
            'attestation_format' => 'none',
            'certificates' => [],
        ]);
    }

    public function test_user_model_has_webauthn_relation(): void
    {
        $user = User::factory()->create();

        $this->assertCount(0, $user->webAuthnCredentials);
        $this->fakeCredentialFor($user, 'YubiKey');
        $user->refresh();
        $this->assertCount(1, $user->webAuthnCredentials);
        $this->assertSame('YubiKey', $user->webAuthnCredentials->first()->alias);
    }

    public function test_webauthn_register_options_route_requires_csrf_and_returns_challenge(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('webauthn.register.options'));

        $response->assertOk();
        $payload = $response->json();
        $this->assertArrayHasKey('challenge', $payload);
        $this->assertArrayHasKey('rp', $payload);
        $this->assertArrayHasKey('user', $payload);
    }

    public function test_webauthn_login_options_route_returns_challenge(): void
    {
        $response = $this->postJson(route('webauthn.login.options'));

        $response->assertOk();
        $this->assertArrayHasKey('challenge', $response->json());
    }

    public function test_profile_lists_users_passkeys(): void
    {
        $user = User::factory()->create();
        $this->fakeCredentialFor($user, 'iPhone');
        $this->fakeCredentialFor($user, 'YubiKey 5');

        Livewire::actingAs($user)
            ->test('profile.webauthn-devices')
            ->assertSee('iPhone')
            ->assertSee('YubiKey 5');
    }

    public function test_user_can_delete_own_passkey(): void
    {
        $user = User::factory()->create();
        $credential = $this->fakeCredentialFor($user, 'Old Phone');

        Livewire::actingAs($user)
            ->test('profile.webauthn-devices')
            ->call('deleteCredential', $credential->id);

        $this->assertDatabaseMissing('webauthn_credentials', ['id' => $credential->id]);
    }

    public function test_user_cannot_delete_another_users_passkey(): void
    {
        $alice = User::factory()->create();
        $bob = User::factory()->create();
        $bobsCred = $this->fakeCredentialFor($bob, 'Bob iPhone');

        Livewire::actingAs($alice)
            ->test('profile.webauthn-devices')
            ->call('deleteCredential', $bobsCred->id);

        // Bob's credential should still be there — Alice can only see/touch her own.
        $this->assertDatabaseHas('webauthn_credentials', ['id' => $bobsCred->id]);
    }

    public function test_admin_can_reset_users_passkeys_via_user_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'user']);
        $this->fakeCredentialFor($target, 'iPhone');
        $this->fakeCredentialFor($target, 'YubiKey');

        $this->assertCount(2, $target->fresh()->webAuthnCredentials);

        Livewire::actingAs($admin)
            ->test(UserIndex::class)
            ->call('resetWebauthn', $target->id);

        $this->assertCount(0, $target->fresh()->webAuthnCredentials);
    }

    public function test_non_admin_cannot_reset_passkeys(): void
    {
        $alice = User::factory()->create(['role' => 'user']);
        $bob = User::factory()->create(['role' => 'user']);
        $cred = $this->fakeCredentialFor($bob, 'Bob iPhone');

        // Non-admin is forbidden at the route level.
        $this->actingAs($alice)
            ->get(route('users.index'))
            ->assertForbidden();

        // Credential should be untouched.
        $this->assertDatabaseHas('webauthn_credentials', ['id' => $cred->id]);
    }

    public function test_manager_cannot_reset_passkeys_either(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $bob = User::factory()->create(['role' => 'user']);
        $cred = $this->fakeCredentialFor($bob, 'Bob iPhone');

        $this->actingAs($manager)
            ->get(route('users.index'))
            ->assertForbidden();

        $this->assertDatabaseHas('webauthn_credentials', ['id' => $cred->id]);
    }

    public function test_admin_reset_only_affects_target_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $alice = User::factory()->create();
        $bob = User::factory()->create();
        $aliceCred = $this->fakeCredentialFor($alice, 'Alice MacBook');
        $bobCred = $this->fakeCredentialFor($bob, 'Bob iPhone');

        Livewire::actingAs($admin)
            ->test(UserIndex::class)
            ->call('resetWebauthn', $alice->id);

        $this->assertDatabaseMissing('webauthn_credentials', ['id' => $aliceCred->id]);
        $this->assertDatabaseHas('webauthn_credentials', ['id' => $bobCred->id]);
    }

    public function test_users_grid_shows_disabled_reset_passkeys_button_when_no_credentials(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee(__('users.reset_webauthn'));
    }
}
