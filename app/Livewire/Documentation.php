<?php

namespace App\Livewire;

use Livewire\Component;

class Documentation extends Component
{
    public string $section = 'overview';

    public function render()
    {
        $sections = [
            'overview' => __('docs.nav_overview'),
            'projects' => __('docs.nav_projects'),
            'views' => __('docs.nav_views'),
            'dashboard' => __('docs.nav_dashboard'),
            'exports' => __('docs.nav_exports'),
            'notifications' => __('docs.nav_notifications'),
            'security' => __('docs.nav_security'),
            'settings' => __('docs.nav_settings'),
            'faq' => __('docs.nav_faq'),
        ];

        return view('livewire.documentation', compact('sections'));
    }
}
