<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectPdfController extends Controller
{
    public function __invoke(Request $request, Project $project)
    {
        Gate::authorize('view', $project);

        $project->load(['phases', 'risks', 'nextSteps', 'projectType']);

        $pdf = Pdf::loadView('pdf.project-report', compact('project'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("project-report-{$project->id}.pdf");
    }
}
