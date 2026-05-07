<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_admin_user(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Full name', 'Test Admin')
            ->expectsQuestion('Username (lowercase letters, numbers, dots, hyphens)', 'test.admin')
            ->expectsQuestion('Email address', 'admin@example.com')
            ->expectsQuestion('Password (min 8 characters)', 'secret123')
            ->expectsQuestion('Confirm password', 'secret123')
            ->expectsOutput('Admin account created: test.admin <admin@example.com>')
            ->assertSuccessful();

        $user = User::where('email', 'admin@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('test.admin', $user->username);
        $this->assertEquals('admin', $user->role);
        $this->assertTrue((bool) $user->is_admin);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_rejects_duplicate_username(): void
    {
        User::factory()->create(['username' => 'test.admin']);

        $this->artisan('app:create-admin')
            ->expectsQuestion('Full name', 'Another Admin')
            ->expectsQuestion('Username (lowercase letters, numbers, dots, hyphens)', 'test.admin')
            ->assertFailed();

        $this->assertEquals(1, User::where('username', 'test.admin')->count());
    }

    public function test_rejects_invalid_username(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Full name', 'Test Admin')
            ->expectsQuestion('Username (lowercase letters, numbers, dots, hyphens)', 'Invalid User!')
            ->assertFailed();

        $this->assertEquals(0, User::count());
    }

    public function test_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'admin@example.com']);

        $this->artisan('app:create-admin')
            ->expectsQuestion('Full name', 'Another Admin')
            ->expectsQuestion('Username (lowercase letters, numbers, dots, hyphens)', 'another.admin')
            ->expectsQuestion('Email address', 'admin@example.com')
            ->assertFailed();

        $this->assertEquals(1, User::where('email', 'admin@example.com')->count());
    }

    public function test_rejects_invalid_email(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Full name', 'Test Admin')
            ->expectsQuestion('Username (lowercase letters, numbers, dots, hyphens)', 'test.admin')
            ->expectsQuestion('Email address', 'not-an-email')
            ->assertFailed();

        $this->assertEquals(0, User::count());
    }

    public function test_rejects_short_password(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Full name', 'Test Admin')
            ->expectsQuestion('Username (lowercase letters, numbers, dots, hyphens)', 'test.admin')
            ->expectsQuestion('Email address', 'admin@example.com')
            ->expectsQuestion('Password (min 8 characters)', 'short')
            ->assertFailed();

        $this->assertEquals(0, User::count());
    }

    public function test_rejects_mismatched_passwords(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Full name', 'Test Admin')
            ->expectsQuestion('Username (lowercase letters, numbers, dots, hyphens)', 'test.admin')
            ->expectsQuestion('Email address', 'admin@example.com')
            ->expectsQuestion('Password (min 8 characters)', 'secret123')
            ->expectsQuestion('Confirm password', 'different123')
            ->assertFailed();

        $this->assertEquals(0, User::count());
    }

    public function test_second_admin_with_different_credentials_is_allowed(): void
    {
        User::factory()->create(['email' => 'first@example.com', 'username' => 'first.admin', 'role' => 'admin', 'is_admin' => true]);

        $this->artisan('app:create-admin')
            ->expectsQuestion('Full name', 'Second Admin')
            ->expectsQuestion('Username (lowercase letters, numbers, dots, hyphens)', 'second.admin')
            ->expectsQuestion('Email address', 'second@example.com')
            ->expectsQuestion('Password (min 8 characters)', 'secret123')
            ->expectsQuestion('Confirm password', 'secret123')
            ->assertSuccessful();

        $this->assertEquals(2, User::where('is_admin', true)->count());
    }
}
