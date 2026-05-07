<?php

namespace Tests\Feature;

use App\Livewire\Forms\LoginForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_enable_two_factor_authentication(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Volt::test('profile.two-factor-authentication-form')
            ->call('enable');

        $user->refresh();

        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);
        $this->assertIsArray($user->two_factor_recovery_codes);
        $this->assertCount(8, $user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
    }

    public function test_user_must_provide_valid_code_to_confirm_two_factor(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('profile.two-factor-authentication-form');
        $component->call('enable');

        $component->set('code', '000000')
            ->call('confirm')
            ->assertHasErrors('code');

        $user->refresh();
        $this->assertNull($user->two_factor_confirmed_at);
    }

    public function test_user_confirms_two_factor_with_valid_code(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('profile.two-factor-authentication-form');
        $component->call('enable');

        $user->refresh();

        $google2fa = new Google2FA();
        $validCode = $google2fa->getCurrentOtp($user->two_factor_secret);

        $component->set('code', $validCode)->call('confirm')->assertHasNoErrors();

        $user->refresh();
        $this->assertNotNull($user->two_factor_confirmed_at);
        $this->assertTrue($user->hasTwoFactorEnabled());
    }

    public function test_user_can_disable_two_factor(): void
    {
        $user = User::factory()->create([
            'two_factor_secret' => 'SECRET',
            'two_factor_recovery_codes' => ['code-1', 'code-2'],
            'two_factor_confirmed_at' => now(),
        ]);
        $this->actingAs($user);

        Volt::test('profile.two-factor-authentication-form')->call('disable');

        $user->refresh();
        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
        $this->assertFalse($user->hasTwoFactorEnabled());
    }

    public function test_login_redirects_to_challenge_when_two_factor_is_enabled(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'two_factor_secret' => 'SECRET',
            'two_factor_confirmed_at' => now(),
        ]);

        Volt::test('pages.auth.login')
            ->set('form.login', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertRedirect(route('two-factor.challenge'));

        $this->assertFalse(auth()->check());
        $this->assertEquals($user->id, session('auth.2fa.user_id'));
    }

    public function test_two_factor_challenge_authenticates_with_valid_code(): void
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ]);

        session(['auth.2fa.user_id' => $user->id, 'auth.2fa.remember' => false]);

        $validCode = $google2fa->getCurrentOtp($secret);

        Volt::test('pages.auth.two-factor-challenge')
            ->set('code', $validCode)
            ->call('submit');

        $this->assertAuthenticatedAs($user);
        $this->assertNull(session('auth.2fa.user_id'));
    }

    public function test_two_factor_challenge_rejects_invalid_code(): void
    {
        $google2fa = new Google2FA();
        $user = User::factory()->create([
            'two_factor_secret' => $google2fa->generateSecretKey(),
            'two_factor_confirmed_at' => now(),
        ]);

        session(['auth.2fa.user_id' => $user->id]);

        Volt::test('pages.auth.two-factor-challenge')
            ->set('code', '000000')
            ->call('submit')
            ->assertHasErrors('code');

        $this->assertGuest();
    }

    public function test_two_factor_challenge_consumes_recovery_code(): void
    {
        $user = User::factory()->create([
            'two_factor_secret' => 'SECRET',
            'two_factor_recovery_codes' => ['recovery-a', 'recovery-b'],
            'two_factor_confirmed_at' => now(),
        ]);

        session(['auth.2fa.user_id' => $user->id, 'auth.2fa.remember' => false]);

        Volt::test('pages.auth.two-factor-challenge')
            ->set('useRecoveryCode', true)
            ->set('recovery_code', 'recovery-a')
            ->call('submit');

        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertEquals(['recovery-b'], array_values($user->two_factor_recovery_codes));
    }

    public function test_disabled_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'is_disabled' => true,
        ]);

        Volt::test('pages.auth.login')
            ->set('form.login', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasErrors(['form.login']);

        $this->assertGuest();
    }
}
