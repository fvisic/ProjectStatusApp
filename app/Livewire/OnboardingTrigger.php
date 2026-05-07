<?php

namespace App\Livewire;

use Livewire\Component;

class OnboardingTrigger extends Component
{
    public function restartTutorial(): void
    {
        auth()->user()->update(['has_completed_onboarding' => false]);
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.onboarding-trigger');
    }
}
