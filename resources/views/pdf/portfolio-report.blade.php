<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<title>{{ __('dashboard.portfolio_report') ?? 'Portfolio Report' }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, sans-serif; color: #172b4d; font-size: 9px; line-height: 1.3; }
    .page { padding: 15px 20px; }
    .header { border-bottom: 2px solid #0052cc; padding-bottom: 8px; margin-bottom: 12px; }
    .header h1 { font-size: 14px; color: #0052cc; }
    .header .meta { font-size: 8px; color: #6b778c; margin-top: 2px; }

    .kpi-row { margin-bottom: 12px; }
    .kpi-row table { width: 100%; }
    .kpi-row td { background: #f4f5f7; border-radius: 4px; padding: 6px 8px; text-align: center; }
    .kpi-row td .label { font-size: 7px; color: #6b778c; text-transform: uppercase; }
    .kpi-row td .val { font-size: 14px; font-weight: 700; color: #172b4d; }

    .project-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .project-table th { background: #0052cc; color: white; padding: 4px 6px; text-align: left; font-size: 8px; font-weight: bold; }
    .project-table td { padding: 4px 6px; border-bottom: 1px solid #ebecf0; font-size: 8px; }
    .project-table tr:nth-child(even) td { background: #f9fafb; }

    .badge { display: inline-block; padding: 1px 5px; border-radius: 8px; font-size: 7px; font-weight: bold; }
    .badge-on_track { background: #e3fcef; color: #006644; }
    .badge-at_risk { background: #fff0b3; color: #974f0c; }
    .badge-off_track { background: #ffebe6; color: #bf2600; }

    .footer { border-top: 1px solid #ebecf0; padding-top: 6px; margin-top: 12px; font-size: 8px; color: #6b778c; text-align: center; }
</style>
</head>
<body>
<div class="page">
    <div class="header">
        <h1>Portfolio Status Report</h1>
        <div class="meta">{{ now()->format('d.m.Y') }} &middot; {{ $projects->count() }} {{ __('dashboard.total_projects') }}</div>
    </div>

    <div class="kpi-row">
        <table cellspacing="4">
            <tr>
                <td>
                    <div class="label">{{ __('dashboard.total_projects') }}</div>
                    <div class="val">{{ $projects->count() }}</div>
                </td>
                <td>
                    <div class="label">🟢 {{ __('projects.health_on_track') }}</div>
                    <div class="val">{{ $healthCounts['on_track'] ?? 0 }}</div>
                </td>
                <td>
                    <div class="label">🟡 {{ __('projects.health_at_risk') }}</div>
                    <div class="val">{{ $healthCounts['at_risk'] ?? 0 }}</div>
                </td>
                <td>
                    <div class="label">🔴 {{ __('projects.health_off_track') }}</div>
                    <div class="val">{{ $healthCounts['off_track'] ?? 0 }}</div>
                </td>
                <td>
                    <div class="label">{{ __('projects.estimated_hours') }}</div>
                    <div class="val">{{ number_format($totalEstimated) }}h</div>
                </td>
                <td>
                    <div class="label">{{ __('projects.spent_hours') }}</div>
                    <div class="val">{{ number_format($totalSpent) }}h</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="project-table">
        <thead>
            <tr>
                <th>{{ __('projects.project_name') }}</th>
                <th>{{ __('projects.client') }}</th>
                <th>{{ __('projects.type') }}</th>
                <th>{{ __('projects.current_phase') }}</th>
                <th>{{ __('projects.overall_health') }}</th>
                <th>{{ __('projects.team_lead') }}</th>
                <th>{{ __('projects.planned_go_live') }}</th>
                <th>{{ __('projects.estimated_hours') }}</th>
                <th>{{ __('projects.spent_hours') }}</th>
                <th>Delta</th>
                <th>{{ __('projects.risks_title') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                @php
                    $forecast = ($project->spent_hours ?? 0) + ($project->remaining_hours ?? 0);
                    $delta = $project->estimated_hours ? round((($forecast - $project->estimated_hours) / $project->estimated_hours) * 100) : 0;
                    $highRisks = $project->risks->where('level', 'high')->count();
                @endphp
                <tr>
                    <td><strong>{{ $project->name }}</strong></td>
                    <td>{{ $project->client ?? '-' }}</td>
                    <td>{{ $project->projectType?->name ?? '-' }}</td>
                    <td>{{ \App\Models\Project::phaseLabel($project->current_phase) }}</td>
                    <td><span class="badge badge-{{ $project->overall_health }}">{{ __('projects.health_' . $project->overall_health) }}</span></td>
                    <td>{{ $project->team_lead ?? '-' }}</td>
                    <td>{{ $project->planned_go_live?->format('d.m.Y') ?? '-' }}</td>
                    <td>{{ $project->estimated_hours ?? 0 }}</td>
                    <td>{{ $project->spent_hours ?? 0 }}</td>
                    <td style="color: {{ $delta > 15 ? '#bf2600' : ($delta > 0 ? '#974f0c' : '#006644') }}; font-weight:bold;">
                        {{ $delta > 0 ? '+' : '' }}{{ $delta }}%
                    </td>
                    <td>{{ $highRisks > 0 ? "🔴 $highRisks" : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ __('projects.status_report') }} &middot; {{ now()->format('d.m.Y H:i') }}
    </div>
</div>
</body>
</html>
