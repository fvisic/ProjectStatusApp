<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<title>{{ $project->name }} - {{ __('projects.status_report') }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: DejaVu Sans, sans-serif; color: #172b4d; font-size: 11px; line-height: 1.4; }

    .page { padding: 20px 24px; }

    .header { border-bottom: 2px solid #0052cc; padding-bottom: 10px; margin-bottom: 14px; }
    .header h1 { font-size: 16px; color: #0052cc; display: inline-block; }
    .header .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; float: right; }
    .badge-new { background: #deebff; color: #0052cc; }
    .badge-migration { background: #e3fcef; color: #006644; }
    .badge-cr { background: #fff0b3; color: #974f0c; }
    .header p { color: #6b778c; font-size: 10px; margin-top: 2px; }

    .meta-grid { width: 100%; margin-bottom: 14px; }
    .meta-grid td { background: #f4f5f7; border-radius: 4px; padding: 6px 8px; vertical-align: top; }
    .meta-grid td label { font-size: 8px; color: #6b778c; text-transform: uppercase; letter-spacing: 0.4px; display: block; margin-bottom: 2px; }
    .meta-grid td span { font-size: 11px; color: #172b4d; }

    h2 { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b778c; margin: 14px 0 8px; }

    .phase-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .phase-table th { background: #0052cc; color: white; padding: 5px 6px; text-align: left; font-size: 9px; font-weight: bold; }
    .phase-table td { padding: 5px 6px; border-bottom: 1px solid #ebecf0; font-size: 10px; }

    .two-col { width: 100%; margin-bottom: 14px; }
    .two-col td { vertical-align: top; width: 50%; }
    .two-col td:first-child { padding-right: 6px; }
    .two-col td:last-child { padding-left: 6px; }

    .card { border: 1px solid #ebecf0; border-radius: 4px; padding: 10px; }
    .card h3 { font-size: 10px; color: #6b778c; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; }

    .est-grid { width: 100%; }
    .est-grid td { text-align: center; background: #f4f5f7; border-radius: 4px; padding: 6px; width: 33.3%; }
    .est-grid td label { font-size: 8px; color: #6b778c; display: block; margin-bottom: 2px; }
    .est-grid td .val { font-size: 13px; font-weight: bold; color: #172b4d; }

    .est-delta { text-align: center; font-size: 10px; padding: 6px; border-radius: 4px; margin-top: 6px; }
    .delta-ok { background: #e3fcef; color: #006644; }
    .delta-warn { background: #fff0b3; color: #974f0c; }
    .delta-over { background: #ffebe6; color: #bf2600; }

    .risk-table { width: 100%; border-collapse: collapse; }
    .risk-table th { font-size: 8px; color: #6b778c; text-transform: uppercase; text-align: left; padding: 3px 6px; }
    .risk-table td { font-size: 10px; padding: 4px 6px; border-bottom: 1px solid #ebecf0; }

    .steps-list { list-style: none; padding: 0; }
    .steps-list li { font-size: 10px; margin-bottom: 4px; }

    .notif-box { background: #fffae6; border: 1px solid #f6c000; border-radius: 4px; padding: 8px; margin-bottom: 14px; }
    .notif-box label { font-size: 9px; color: #974f0c; font-weight: bold; display: block; margin-bottom: 2px; }
    .notif-box .val { font-size: 10px; }

    .footer { border-top: 1px solid #ebecf0; padding-top: 8px; margin-top: 14px; font-size: 9px; color: #6b778c; }
    .footer table { width: 100%; }
    .footer td { padding: 0 4px; }
</style>
</head>
<body>
<div class="page">
    <div class="header">
        <h1>{{ __('projects.status_report') }}</h1>
        @if ($project->projectType)
            <span class="badge" style="{{ $project->projectType->pdfBadgeStyle() }}">{{ $project->projectType->name }}</span>
        @endif
        <p>{{ $project->report_date?->format('d.m.Y') ?? now()->format('d.m.Y') }}</p>
    </div>

    <table class="meta-grid" cellspacing="4">
        <tr>
            <td><label>{{ __('projects.project_name') }}</label><span>{{ $project->name }}</span></td>
            <td><label>{{ __('projects.client') }}</label><span>{{ $project->client ?? '-' }}</span></td>
            <td><label>{{ __('projects.team_lead') }}</label><span>{{ $project->team_lead ?? '-' }}</span></td>
            <td><label>{{ __('projects.report_date') }}</label><span>{{ $project->report_date?->format('d.m.Y') ?? '-' }}</span></td>
        </tr>
        <tr>
            <td><label>{{ __('projects.project_start') }}</label><span>{{ $project->project_start?->format('d.m.Y') ?? '-' }}</span></td>
            <td><label>{{ __('projects.planned_go_live') }}</label><span>{{ $project->planned_go_live?->format('d.m.Y') ?? '-' }}</span></td>
            <td><label>{{ __('projects.current_phase') }}</label><span>{{ \App\Models\Project::phaseLabel($project->current_phase) }}</span></td>
            <td><label>{{ __('projects.overall_health') }}</label><span>{{ __('projects.health_' . $project->overall_health) }}</span></td>
        </tr>
    </table>

    <h2>{{ __('projects.phases_title') }}</h2>
    <table class="phase-table">
        <thead>
            <tr>
                <th>{{ __('projects.phase') }}</th>
                <th>{{ __('projects.key_activities') }}</th>
                <th>{{ __('projects.client_confirmation') }}</th>
                <th>{{ __('projects.status') }}</th>
                <th>{{ __('projects.date') }}</th>
                <th>{{ __('projects.notes') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($project->phases as $phase)
                <tr>
                    <td><strong>{{ $phase->phase_name }}</strong></td>
                    <td>{{ $phase->key_activities ?? '' }}</td>
                    <td>{{ $phase->client_confirmation ?? '-' }}</td>
                    <td>{{ __('projects.status_' . $phase->status) }}</td>
                    <td>{{ $phase->completion_date?->format('d.m.Y') ?? '' }}</td>
                    <td>{{ $phase->notes ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="two-col">
        <tr>
            <td>
                <div class="card">
                    <h3>{{ __('projects.estimation_title') }}</h3>
                    <table class="est-grid" cellspacing="4">
                        <tr>
                            <td><label>{{ __('projects.estimated_hours') }}</label><div class="val">{{ $project->estimated_hours ?? 0 }}</div></td>
                            <td><label>{{ __('projects.spent_hours') }}</label><div class="val">{{ $project->spent_hours ?? 0 }}</div></td>
                            <td><label>{{ __('projects.remaining_hours') }}</label><div class="val">{{ $project->remaining_hours ?? 0 }}</div></td>
                        </tr>
                    </table>
                    @if ($project->estimated_hours)
                        @php
                            $forecast = ($project->spent_hours ?? 0) + ($project->remaining_hours ?? 0);
                            $delta = $forecast - $project->estimated_hours;
                            $pct = $project->estimated_hours > 0 ? round(($delta / $project->estimated_hours) * 100) : 0;
                        @endphp
                        <div class="est-delta {{ $delta <= 0 ? 'delta-ok' : ($pct <= 15 ? 'delta-warn' : 'delta-over') }}">
                            @if ($delta <= 0)
                                {{ __('projects.delta_ok', ['forecast' => $forecast, 'pct' => abs($pct)]) }}
                            @elseif ($pct <= 15)
                                {{ __('projects.delta_warn', ['delta' => $delta, 'pct' => $pct]) }}
                            @else
                                {{ __('projects.delta_over', ['delta' => $delta, 'pct' => $pct]) }}
                            @endif
                        </div>
                    @endif
                    @if ($project->estimation_comment)
                        <p style="margin-top:6px; font-size:10px; color:#6b778c;">{{ $project->estimation_comment }}</p>
                    @endif
                </div>
            </td>
            <td>
                <div class="card">
                    <h3>{{ __('projects.next_steps_title') }}</h3>
                    <ul class="steps-list">
                        @foreach ($project->nextSteps as $step)
                            <li>{{ $step->is_completed ? '✓' : '○' }} {{ $step->description }}</li>
                        @endforeach
                    </ul>
                </div>
            </td>
        </tr>
    </table>

    @if ($project->risks->count())
        <div class="card" style="margin-bottom:14px;">
            <h3>{{ __('projects.risks_title') }}</h3>
            <table class="risk-table">
                <thead>
                    <tr>
                        <th>{{ __('projects.risk_description') }}</th>
                        <th>{{ __('projects.risk_level') }}</th>
                        <th>{{ __('projects.risk_mitigation') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($project->risks as $risk)
                        <tr>
                            <td>{{ $risk->description ?? '' }}</td>
                            <td>{{ __('projects.level_' . $risk->level) }}</td>
                            <td>{{ $risk->mitigation ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($project->product_notification_description || $project->product_notification_deadline)
        <div class="notif-box">
            <table cellspacing="4" style="width:100%">
                <tr>
                    <td style="width:50%">
                        <label>{{ __('projects.notification_deadline') }}</label>
                        <div class="val">{{ $project->product_notification_deadline?->format('d.m.Y') ?? '-' }}</div>
                    </td>
                    <td style="width:50%">
                        <label>{{ __('projects.notification_duration') }}</label>
                        <div class="val">{{ $project->product_notification_duration ?? '-' }}</div>
                    </td>
                </tr>
                @if ($project->product_notification_description)
                    <tr>
                        <td colspan="2">
                            <label>{{ __('projects.notification_description') }}</label>
                            <div class="val">{{ $project->product_notification_description }}</div>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    <div class="footer">
        <table>
            <tr>
                <td>{{ __('projects.filled_by') }}: {{ $project->filled_by ?? '-' }}</td>
                <td>{{ __('projects.reviewed_by') }}: {{ $project->reviewed_by ?? '-' }}</td>
                <td style="text-align:right;">{{ __('projects.version') }}: {{ $project->version }}</td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
