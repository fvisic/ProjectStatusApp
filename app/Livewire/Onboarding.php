<?php

namespace App\Livewire;

use Livewire\Component;

class Onboarding extends Component
{
    public bool $show = false;
    public int $step = 1;
    public int $totalSteps = 7;

    public function mount(): void
    {
        $this->show = !auth()->user()->has_completed_onboarding;
    }

    public function nextStep(): void
    {
        if ($this->step < $this->totalSteps) {
            $this->step++;
        }
    }

    public function prevStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function complete(): void
    {
        auth()->user()->update(['has_completed_onboarding' => true]);
        $this->show = false;
    }

    public function skip(): void
    {
        $this->complete();
    }

    public function restart(): void
    {
        $this->step = 1;
        $this->show = true;
    }

    public function render()
    {
        return view('livewire.onboarding');
    }
}
