<?php

namespace App\Livewire;

use Livewire\Component;

class LocaleSwitcher extends Component
{
    public string $locale;

    public function mount(): void
    {
        $this->locale = session('locale', auth()->user()?->locale ?? config('app.locale'));
    }

    public function switchLocale(string $locale): void
    {
        if (! in_array($locale, ['hr', 'en', 'de'])) {
            return;
        }

        $this->locale = $locale;
        session(['locale' => $locale]);
        app()->setLocale($locale);

        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        $this->redirect(request()->header('Referer', '/'), navigate: true);
    }

    public function render()
    {
        return view('livewire.locale-switcher');
    }
}
