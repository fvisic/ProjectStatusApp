<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PortfolioPdfController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->user()->isAdminOrManager()
            ? Project::query()
            : Project::where('created_by', $request->user()->id);

        $projects = $query->with(['phases', 'risks', 'projectType'])->orderBy('name')->get();

        $healthCounts = $projects->groupBy('overall_health')->map->count();
        $totalEstimated = $projects->sum('estimated_hours') ?: 0;
        $totalSpent = $projects->sum('spent_hours') ?: 0;
        $totalRemaining = $projects->sum('remaining_hours') ?: 0;

        $pdf = Pdf::loadView('pdf.portfolio-report', compact('projects', 'healthCounts', 'totalEstimated', 'totalSpent', 'totalRemaining'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('portfolio-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
