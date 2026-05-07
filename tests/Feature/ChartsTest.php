<?php

namespace Tests\Feature;

use App\Livewire\Dashboard;
use App\Livewire\ProjectForm;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChartsTest extends TestCase
{
    use RefreshDatabase;

    public function test_chartjs_is_registered_in_app_js(): void
    {
        $appJs = file_get_contents(base_path('resources/js/app.js'));
        $this->assertStringContainsString('chart.js', $appJs);
        $this->assertStringContainsString('window.Chart', $appJs);
    }

    public function test_chartjs_package_is_installed(): void
    {
        $pkg = json_decode(file_get_contents(base_path('package.json')), true);
        $hasChartjs = isset($pkg['dependencies']['chart.js']) || isset($pkg['devDependencies']['chart.js']);
        $this->assertTrue($hasChartjs, 'chart.js should be in package.json dependencies');
    }

    public function test_dashboard_replaces_sparklines_with_canvas(): void
    {
        $html = file_get_contents(base_path('resources/views/livewire/dashboard.blade.php'));

        // Expect at least 3 Chart.js canvas components: health trend, spent trend, phase distribution
        $canvasCount = substr_count($html, '<canvas');
        $this->assertGreaterThanOrEqual(3, $canvasCount);

        // Chart.js init functions should be present
        $this->assertStringContainsString('healthTrendChart', $html);
        $this->assertStringContainsString('spentTrendChart', $html);
        $this->assertStringContainsString('phaseDistributionChart', $html);
    }

    public function test_burndown_chart_uses_canvas(): void
    {
        $html = file_get_contents(base_path('resources/views/livewire/project-form.blade.php'));
        $this->assertStringContainsString('burndownChart', $html);
        $this->assertStringContainsString('<canvas', $html);
    }

    public function test_dashboard_renders_with_charts(): void
    {
        $user = User::factory()->create();
        Project::factory()->count(3)->create(['created_by' => $user->id]);

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->assertOk()
            ->assertSeeHtml('canvas');
    }

    public function test_project_form_burndown_tab_renders_canvas(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['created_by' => $user->id]);

        // Create snapshots so burndown has data
        $project->createSnapshot($user->id, 'snap1');
        $project->createSnapshot($user->id, 'snap2');

        Livewire::actingAs($user)
            ->test(ProjectForm::class, ['project' => $project])
            ->assertOk()
            ->assertSeeHtml('burndownChart');
    }

    public function test_compiled_assets_include_chartjs(): void
    {
        $manifest = base_path('public/build/manifest.json');
        if (! file_exists($manifest)) {
            $this->markTestSkipped('Assets not built');
        }

        $manifestData = json_decode(file_get_contents($manifest), true);
        $appEntry = $manifestData['resources/js/app.js'] ?? null;
        if (! $appEntry || ! isset($appEntry['file'])) {
            $this->markTestSkipped('app.js entry missing from manifest');
        }

        $jsPath = base_path('public/build/' . $appEntry['file']);
        if (! file_exists($jsPath)) {
            $this->markTestSkipped('JS bundle missing');
        }

        $contents = file_get_contents($jsPath);
        // Chart.js should be registered on window
        $this->assertStringContainsString('Chart', $contents);
    }
}
