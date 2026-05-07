<?php

namespace Tests\Feature;

use App\Livewire\Documentation;
use App\Livewire\Onboarding;
use App\Livewire\OnboardingTrigger;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OnboardingDocsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['has_completed_onboarding' => false]);
    }

    // --- Onboarding ---

    public function test_onboarding_shows_for_new_user(): void
    {
        Livewire::actingAs($this->user)
            ->test(Onboarding::class)
            ->assertSet('show', true)
            ->assertSet('step', 1);
    }

    public function test_onboarding_hidden_for_completed_user(): void
    {
        $user = User::factory()->create(['has_completed_onboarding' => true]);

        Livewire::actingAs($user)
            ->test(Onboarding::class)
            ->assertSet('show', false);
    }

    public function test_onboarding_next_and_prev(): void
    {
        Livewire::actingAs($this->user)
            ->test(Onboarding::class)
            ->assertSet('step', 1)
            ->call('nextStep')
            ->assertSet('step', 2)
            ->call('nextStep')
            ->assertSet('step', 3)
            ->call('prevStep')
            ->assertSet('step', 2);
    }

    public function test_onboarding_complete_saves_to_db(): void
    {
        Livewire::actingAs($this->user)
            ->test(Onboarding::class)
            ->call('complete');

        $this->assertTrue($this->user->fresh()->has_completed_onboarding);
    }

    public function test_onboarding_skip_completes(): void
    {
        Livewire::actingAs($this->user)
            ->test(Onboarding::class)
            ->call('skip')
            ->assertSet('show', false);

        $this->assertTrue($this->user->fresh()->has_completed_onboarding);
    }

    public function test_onboarding_step_clamps(): void
    {
        Livewire::actingAs($this->user)
            ->test(Onboarding::class)
            ->call('prevStep') // can't go below 1
            ->assertSet('step', 1);
    }

    public function test_onboarding_walks_all_steps(): void
    {
        $component = Livewire::actingAs($this->user)
            ->test(Onboarding::class);

        for ($i = 1; $i < 7; $i++) {
            $component->call('nextStep');
        }
        $component->assertSet('step', 7);

        // Can't go past last step
        $component->call('nextStep')->assertSet('step', 7);
    }

    // --- Onboarding Trigger ---

    public function test_restart_tutorial_resets_flag(): void
    {
        $user = User::factory()->create(['has_completed_onboarding' => true]);

        Livewire::actingAs($user)
            ->test(OnboardingTrigger::class)
            ->call('restartTutorial');

        $this->assertFalse($user->fresh()->has_completed_onboarding);
    }

    // --- Documentation ---

    public function test_docs_page_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(Documentation::class)
            ->assertOk()
            ->assertSet('section', 'overview');
    }

    public function test_docs_section_switching(): void
    {
        Livewire::actingAs($this->user)
            ->test(Documentation::class)
            ->set('section', 'projects')
            ->assertSet('section', 'projects')
            ->set('section', 'faq')
            ->assertSet('section', 'faq');
    }

    public function test_docs_route_accessible(): void
    {
        $response = $this->actingAs($this->user)->get(route('docs'));
        $response->assertOk();
    }
}
