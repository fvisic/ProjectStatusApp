<?php

use App\Exports\ProjectsExport;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\PortfolioPdfController;
use App\Http\Controllers\ProjectPdfController;
use App\Livewire\Dashboard;
use App\Livewire\Documentation;
use App\Livewire\ProjectForm;
use App\Livewire\ProjectHistory;
use App\Livewire\ProjectIndex;
use App\Livewire\ProjectKanban;
use App\Livewire\ProjectTimeline;
use App\Livewire\ProjectTypeIndex;
use App\Livewire\UserIndex;
use Illuminate\Support\Facades\Route;
use Laragear\WebAuthn\Http\Routes as WebAuthnRoutes;
use Maatwebsite\Excel\Facades\Excel;

WebAuthnRoutes::register();

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    Route::get('projects', ProjectIndex::class)->name('projects.index');
    Route::get('projects/kanban', ProjectKanban::class)->name('projects.kanban');
    Route::get('projects/timeline', ProjectTimeline::class)->name('projects.timeline');
    Route::get('projects/create', ProjectForm::class)->name('projects.create');
    Route::get('projects/{projectId}/edit', ProjectForm::class)->name('projects.edit');
    Route::get('projects/{projectId}/history', ProjectHistory::class)->name('projects.history');
    Route::get('projects/{project}/pdf', ProjectPdfController::class)->name('projects.pdf');

    Route::get('projects/portfolio-pdf', PortfolioPdfController::class)->name('projects.portfolio');

    Route::get('docs', Documentation::class)->name('docs');

    Route::get('projects/export/{format}', function (string $format) {
        $export = new ProjectsExport(
            userId: auth()->id(),
            isAdmin: auth()->user()->isAdminOrManager(),
            filterHealth: request('health', ''),
            filterType: request('type', ''),
        );
        $filename = 'projects-' . now()->format('Y-m-d');
        return match ($format) {
            'csv' => $export->download("$filename.csv", \Maatwebsite\Excel\Excel::CSV),
            default => $export->download("$filename.xlsx"),
        };
    })->name('projects.export');

    Route::post('impersonate/stop', [ImpersonationController::class, 'stop'])->name('impersonate.stop');
    Route::post('impersonate/{user}', [ImpersonationController::class, 'start'])->name('impersonate.start');

    Route::get('users', UserIndex::class)->name('users.index');
    Route::get('project-types', ProjectTypeIndex::class)->name('project-types.index');
});

require __DIR__.'/auth.php';
