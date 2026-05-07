<?php

namespace Tests\Feature;

use Tests\TestCase;

class MobileResponsivenessTest extends TestCase
{
    public function test_project_index_hides_columns_on_mobile(): void
    {
        $html = file_get_contents(base_path('resources/views/livewire/project-index.blade.php'));

        // Client + Type columns should have hidden md:table-cell
        $this->assertStringContainsString('hidden md:table-cell', $html);

        // Projects table wrapped for horizontal scrolling
        $this->assertStringContainsString('overflow-x-auto', $html);
    }

    public function test_project_index_export_buttons_hidden_on_mobile(): void
    {
        $html = file_get_contents(base_path('resources/views/livewire/project-index.blade.php'));
        $this->assertStringContainsString('hidden sm:inline-flex', $html);
    }

    public function test_project_form_tabs_wrap_on_mobile(): void
    {
        $html = file_get_contents(base_path('resources/views/livewire/project-form.blade.php'));
        $this->assertStringContainsString('flex-wrap', $html);
        $this->assertStringContainsString('w-full sm:w-36', $html);
    }

    public function test_project_history_diff_table_scrolls(): void
    {
        $html = file_get_contents(base_path('resources/views/livewire/project-history.blade.php'));
        // Count overflow-x-auto - should wrap diff table + phases table (>= 2)
        $count = substr_count($html, 'overflow-x-auto');
        $this->assertGreaterThanOrEqual(2, $count);
    }

    public function test_project_timeline_controls_wrap(): void
    {
        $html = file_get_contents(base_path('resources/views/livewire/project-timeline.blade.php'));
        $this->assertStringContainsString('flex-wrap', $html);
    }

    public function test_dashboard_has_responsive_kpi_grid(): void
    {
        $html = file_get_contents(base_path('resources/views/livewire/dashboard.blade.php'));
        // Grid should not be fixed at grid-cols-4 without responsive prefix
        $this->assertMatchesRegularExpression(
            '/grid-cols-(1|2)\s+.*?md:grid-cols-/',
            $html,
            'Dashboard should use responsive grid-cols for KPI cards'
        );
    }

    public function test_documentation_has_mobile_nav(): void
    {
        $html = file_get_contents(base_path('resources/views/livewire/documentation.blade.php'));
        // Desktop sidebar hidden on mobile
        $this->assertStringContainsString('hidden md:block', $html);
        // Mobile select dropdown
        $this->assertStringContainsString('md:hidden', $html);
    }

    public function test_onboarding_feature_grid_responsive(): void
    {
        $html = file_get_contents(base_path('resources/views/livewire/onboarding.blade.php'));
        $this->assertStringContainsString('grid-cols-1 sm:grid-cols-3', $html);
    }

    public function test_navigation_has_notification_in_responsive_menu(): void
    {
        $html = file_get_contents(base_path('resources/views/livewire/layout/navigation.blade.php'));
        $count = substr_count($html, 'livewire:notification-center');
        // At least 2: once in main navbar, once in responsive/hamburger menu
        $this->assertGreaterThanOrEqual(2, $count);
    }
}
