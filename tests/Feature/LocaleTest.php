<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use App\Livewire\LocaleSwitcher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['locale' => 'hr']);
    }

    // --- LocaleSwitcher Component ---

    public function test_locale_switcher_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(LocaleSwitcher::class)
            ->assertOk();
    }

    public function test_locale_switcher_mounts_from_session(): void
    {
        session(['locale' => 'en']);

        Livewire::actingAs($this->user)
            ->test(LocaleSwitcher::class)
            ->assertSet('locale', 'en');
    }

    public function test_locale_switcher_mounts_from_user_when_no_session(): void
    {
        // No session locale set, user has locale 'hr'
        Livewire::actingAs($this->user)
            ->test(LocaleSwitcher::class)
            ->assertSet('locale', 'hr');
    }

    public function test_locale_switcher_uses_user_locale_as_fallback(): void
    {
        // User with default locale 'hr', no session set
        $user = User::factory()->create(['locale' => 'hr']);

        Livewire::actingAs($user)
            ->test(LocaleSwitcher::class)
            ->assertSet('locale', 'hr');
    }

    public function test_switch_locale_to_en(): void
    {
        Livewire::actingAs($this->user)
            ->test(LocaleSwitcher::class)
            ->call('switchLocale', 'en')
            ->assertRedirect();

        $this->assertEquals('en', session('locale'));
        $this->assertEquals('en', $this->user->fresh()->locale);
    }

    public function test_switch_locale_to_hr(): void
    {
        $user = User::factory()->create(['locale' => 'en']);

        Livewire::actingAs($user)
            ->test(LocaleSwitcher::class)
            ->call('switchLocale', 'hr')
            ->assertRedirect();

        $this->assertEquals('hr', session('locale'));
        $this->assertEquals('hr', $user->fresh()->locale);
    }

    public function test_switch_locale_rejects_invalid_locale(): void
    {
        Livewire::actingAs($this->user)
            ->test(LocaleSwitcher::class)
            ->call('switchLocale', 'fr');

        // Locale should not change
        $this->assertEquals('hr', $this->user->fresh()->locale);
    }

    public function test_switch_locale_to_de(): void
    {
        Livewire::actingAs($this->user)
            ->test(LocaleSwitcher::class)
            ->call('switchLocale', 'de')
            ->assertRedirect();

        $this->assertEquals('de', session('locale'));
        $this->assertEquals('de', $this->user->fresh()->locale);
    }

    public function test_switch_locale_updates_session(): void
    {
        Livewire::actingAs($this->user)
            ->test(LocaleSwitcher::class)
            ->call('switchLocale', 'en');

        $this->assertEquals('en', session('locale'));
    }

    // --- SetLocale Middleware ---

    public function test_middleware_sets_locale_from_session(): void
    {
        session(['locale' => 'hr']);

        $this->actingAs($this->user)
            ->get(route('dashboard'));

        $this->assertEquals('hr', app()->getLocale());
    }

    public function test_middleware_sets_locale_from_user_when_no_session(): void
    {
        $user = User::factory()->create(['locale' => 'en']);

        $this->actingAs($user)
            ->get(route('dashboard'));

        $this->assertEquals('en', app()->getLocale());
    }

    public function test_middleware_ignores_invalid_session_locale(): void
    {
        $defaultLocale = config('app.locale');

        // Manually set an invalid locale in session
        session(['locale' => 'xx']);

        $middleware = new SetLocale();
        $request = Request::create('/test');
        $request->setUserResolver(fn () => $this->user);

        $middleware->handle($request, fn () => response('ok'));

        // Should remain at default since 'xx' is not in ['hr', 'en', 'de']
        $this->assertEquals($defaultLocale, app()->getLocale());
    }

    public function test_middleware_session_takes_priority_over_user(): void
    {
        session(['locale' => 'en']);
        $user = User::factory()->create(['locale' => 'hr']);

        $this->actingAs($user)
            ->get(route('dashboard'));

        $this->assertEquals('en', app()->getLocale());
    }

    public function test_middleware_uses_default_locale_for_user_without_custom_locale(): void
    {
        // User with default 'hr' locale, no session set
        $user = User::factory()->create(['locale' => 'hr']);

        $this->actingAs($user)
            ->get(route('dashboard'));

        $this->assertEquals('hr', app()->getLocale());
    }
}
