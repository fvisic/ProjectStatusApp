<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Smalot\PdfParser\Parser;
use Tests\TestCase;

class PdfDiacriticsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regression guard: dompdf silently drops glyphs like č/đ/ć when `font-weight: 600`
     * is used because it maps the weight to a synthetic bold that lacks those glyphs.
     * Only `normal` (400) and `bold` (700) are safe.
     */
    public function test_pdf_templates_do_not_use_font_weight_600(): void
    {
        $paths = [
            resource_path('views/pdf/project-report.blade.php'),
            resource_path('views/pdf/portfolio-report.blade.php'),
        ];

        foreach ($paths as $path) {
            $content = file_get_contents($path);
            $this->assertDoesNotMatchRegularExpression(
                '/font-weight\s*:\s*600/',
                $content,
                "PDF template {$path} uses font-weight: 600, which breaks š/đ/č/ć/ž glyphs. Use `bold` (700) instead."
            );
        }
    }

    public function test_project_pdf_renders_croatian_diacritics_in_bold_headers(): void
    {
        app()->setLocale('hr');

        $user = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create([
            'created_by' => $user->id,
            'name' => 'Čćšđž Project',
            'client' => 'Kraš d.d.',
            'team_lead' => 'Ana Kovačević',
        ]);

        $response = $this->actingAs($user)->get(route('projects.pdf', $project));
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');

        $pdfBytes = $response->getContent();

        $parser = new Parser();
        $text = $parser->parseContent($pdfBytes)->getText();

        // Bold table header — was "Klju?ne aktivnosti" before the fix
        $this->assertStringContainsString('Ključne aktivnosti', $text);

        // Regular-weight content
        $this->assertStringContainsString('Čćšđž Project', $text);
        $this->assertStringContainsString('Ana Kovačević', $text);
        $this->assertStringContainsString('Kraš', $text);
    }
}
