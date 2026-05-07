<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\ProjectPhase;
use App\Models\ProjectType;
use App\Models\User;
use App\Notifications\ProjectAlertNotification;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = collect([
            User::factory()->create([
                'name'     => 'Sarah Chen',
                'username' => 'sarah.chen',
                'email'    => 'sarah@example.com',
                'password' => bcrypt('Demo1234!'),
                'email_verified_at' => now(),
                'locale'   => 'en',
                'is_admin' => true,
                'role'     => 'admin',
            ]),
            User::factory()->create([
                'name'     => 'James Miller',
                'username' => 'james.miller',
                'email'    => 'james@example.com',
                'password' => bcrypt('Demo1234!'),
                'email_verified_at' => now(),
                'locale'   => 'en',
                'role'     => 'manager',
            ]),
            User::factory()->create([
                'name'     => 'Priya Sharma',
                'username' => 'priya.sharma',
                'email'    => 'priya@example.com',
                'password' => bcrypt('Demo1234!'),
                'email_verified_at' => now(),
                'locale'   => 'en',
            ]),
            User::factory()->create([
                'name'     => 'Tom Weber',
                'username' => 'tom.weber',
                'email'    => 'tom@example.com',
                'password' => bcrypt('Demo1234!'),
                'email_verified_at' => now(),
                'locale'   => 'en',
            ]),
        ]);

        $typeMap = [
            'new'       => ProjectType::where('sort_order', 1)->value('id'),
            'migration' => ProjectType::where('sort_order', 2)->value('id'),
            'cr'        => ProjectType::where('sort_order', 3)->value('id'),
        ];

        $projects = [
            [
                'name' => 'H&M ERP Migration',
                'client' => 'H&M Group',
                'project_type' => 'migration',
                'current_phase' => 'implementacija_testiranje',
                'overall_health' => 'on_track',
                'estimated_hours' => 480,
                'spent_hours' => 220,
                'remaining_hours' => 200,
                'project_start' => now()->subMonths(3),
                'planned_go_live' => now()->addMonths(2),
                'team_lead' => 'Sarah Chen',
                'filled_by' => 'Sarah Chen',
                'reviewed_by' => 'James Miller',
            ],
            [
                'name' => 'Booking.com Loyalty Platform',
                'client' => 'Booking.com',
                'project_type' => 'new',
                'current_phase' => 'funkcionalna_specifikacija',
                'overall_health' => 'at_risk',
                'estimated_hours' => 720,
                'spent_hours' => 180,
                'remaining_hours' => 620,
                'project_start' => now()->subMonths(2),
                'planned_go_live' => now()->addMonths(4),
                'team_lead' => 'James Miller',
                'estimation_comment' => 'Client is late approving the functional spec — scope overrun likely.',
                'filled_by' => 'James Miller',
                'reviewed_by' => 'Sarah Chen',
            ],
            [
                'name' => 'Adyen Payment Gateway',
                'client' => 'Adyen N.V.',
                'project_type' => 'new',
                'current_phase' => 'integracije',
                'overall_health' => 'on_track',
                'estimated_hours' => 360,
                'spent_hours' => 280,
                'remaining_hours' => 60,
                'project_start' => now()->subMonths(5),
                'planned_go_live' => now()->addDays(18),
                'team_lead' => 'Priya Sharma',
                'filled_by' => 'Priya Sharma',
                'reviewed_by' => 'Sarah Chen',
            ],
            [
                'name' => 'Lonza LIMS Upgrade',
                'client' => 'Lonza Group',
                'project_type' => 'cr',
                'current_phase' => 'uat_edukacija',
                'overall_health' => 'on_track',
                'estimated_hours' => 200,
                'spent_hours' => 160,
                'remaining_hours' => 30,
                'project_start' => now()->subMonths(4),
                'planned_go_live' => now()->addDays(8),
                'team_lead' => 'Tom Weber',
                'filled_by' => 'Tom Weber',
                'reviewed_by' => 'James Miller',
            ],
            [
                'name' => 'Unilever Supply Chain',
                'client' => 'Unilever PLC',
                'project_type' => 'new',
                'current_phase' => 'instalacija_analiza',
                'overall_health' => 'on_track',
                'estimated_hours' => 600,
                'spent_hours' => 40,
                'remaining_hours' => 540,
                'project_start' => now()->subWeeks(2),
                'planned_go_live' => now()->addMonths(6),
                'team_lead' => 'Sarah Chen',
                'filled_by' => 'Sarah Chen',
                'reviewed_by' => 'James Miller',
            ],
            [
                'name' => 'Rivian HR System',
                'client' => 'Rivian Automotive',
                'project_type' => 'new',
                'current_phase' => 'implementacija_testiranje',
                'overall_health' => 'off_track',
                'estimated_hours' => 300,
                'spent_hours' => 290,
                'remaining_hours' => 120,
                'project_start' => now()->subMonths(4),
                'planned_go_live' => now()->subDays(5),
                'team_lead' => 'James Miller',
                'estimation_comment' => 'Significant scope creep — performance review modules added after original spec was signed off.',
                'filled_by' => 'James Miller',
                'reviewed_by' => 'Sarah Chen',
                'product_notification_description' => 'Performance review module needs additional development — 3 reports missing.',
                'product_notification_duration' => '5–8 business days',
                'product_notification_deadline' => now()->addDays(3),
            ],
            [
                'name' => 'Zendesk Billing v3',
                'client' => 'Zendesk Inc.',
                'project_type' => 'cr',
                'current_phase' => 'go_live',
                'overall_health' => 'on_track',
                'estimated_hours' => 150,
                'spent_hours' => 140,
                'remaining_hours' => 5,
                'project_start' => now()->subMonths(3),
                'planned_go_live' => now()->addDays(2),
                'team_lead' => 'Priya Sharma',
                'filled_by' => 'Priya Sharma',
                'reviewed_by' => 'Sarah Chen',
            ],
            [
                'name' => 'DHL WMS Integration',
                'client' => 'DHL Supply Chain',
                'project_type' => 'new',
                'current_phase' => 'implementacija_testiranje',
                'overall_health' => 'at_risk',
                'estimated_hours' => 400,
                'spent_hours' => 250,
                'remaining_hours' => 200,
                'project_start' => now()->subMonths(4),
                'planned_go_live' => now()->addMonths(1),
                'team_lead' => 'Tom Weber',
                'estimation_comment' => 'WMS integration more complex than scoped — API documentation incomplete on client side.',
                'filled_by' => 'Tom Weber',
                'reviewed_by' => 'Sarah Chen',
            ],
            [
                'name' => 'BASF MES Platform',
                'client' => 'BASF SE',
                'project_type' => 'new',
                'current_phase' => 'funkcionalna_specifikacija',
                'overall_health' => 'on_track',
                'estimated_hours' => 520,
                'spent_hours' => 80,
                'remaining_hours' => 420,
                'project_start' => now()->subMonths(1),
                'planned_go_live' => now()->addMonths(5),
                'team_lead' => 'Sarah Chen',
                'filled_by' => 'Sarah Chen',
                'reviewed_by' => 'James Miller',
            ],
            [
                'name' => 'Spotify CRM Migration',
                'client' => 'Spotify AB',
                'project_type' => 'migration',
                'current_phase' => 'hypercare',
                'overall_health' => 'on_track',
                'estimated_hours' => 350,
                'spent_hours' => 340,
                'remaining_hours' => 0,
                'project_start' => now()->subMonths(7),
                'planned_go_live' => now()->subMonths(1),
                'team_lead' => 'James Miller',
                'filled_by' => 'James Miller',
                'reviewed_by' => 'Sarah Chen',
                'version' => 'v3.2',
            ],
            [
                'name' => 'Siemens Smart Metering',
                'client' => 'Siemens AG',
                'project_type' => 'new',
                'current_phase' => 'integracije',
                'overall_health' => 'at_risk',
                'estimated_hours' => 800,
                'spent_hours' => 520,
                'remaining_hours' => 350,
                'project_start' => now()->subMonths(6),
                'planned_go_live' => now()->addMonths(2),
                'team_lead' => 'Priya Sharma',
                'estimation_comment' => 'IoT integration with third-party provider requires additional iterations.',
                'filled_by' => 'Priya Sharma',
                'reviewed_by' => 'Sarah Chen',
                'product_notification_description' => 'MQTT broker configuration required before production release.',
                'product_notification_duration' => '2–3 business days',
                'product_notification_deadline' => now()->addWeeks(2),
            ],
            [
                'name' => 'Hilton Booking Engine',
                'client' => 'Hilton Worldwide',
                'project_type' => 'new',
                'current_phase' => 'implementacija_testiranje',
                'overall_health' => 'on_track',
                'estimated_hours' => 550,
                'spent_hours' => 300,
                'remaining_hours' => 220,
                'project_start' => now()->subMonths(4),
                'planned_go_live' => now()->addMonths(2)->subDays(5),
                'team_lead' => 'Tom Weber',
                'filled_by' => 'Tom Weber',
                'reviewed_by' => 'Sarah Chen',
            ],
            [
                'name' => 'NHS e-Prescriptions v2',
                'client' => 'NHS England',
                'project_type' => 'cr',
                'current_phase' => 'uat_edukacija',
                'overall_health' => 'on_track',
                'estimated_hours' => 250,
                'spent_hours' => 210,
                'remaining_hours' => 25,
                'project_start' => now()->subMonths(3),
                'planned_go_live' => now()->addDays(12),
                'team_lead' => 'Sarah Chen',
                'filled_by' => 'Sarah Chen',
                'reviewed_by' => 'James Miller',
            ],
            [
                'name' => 'Cloudflare Infrastructure Migration',
                'client' => 'Cloudflare Inc.',
                'project_type' => 'migration',
                'current_phase' => 'instalacija_analiza',
                'overall_health' => 'on_track',
                'estimated_hours' => 300,
                'spent_hours' => 20,
                'remaining_hours' => 270,
                'project_start' => now()->subWeeks(1),
                'planned_go_live' => now()->addMonths(4),
                'team_lead' => 'Priya Sharma',
                'filled_by' => 'Priya Sharma',
                'reviewed_by' => 'James Miller',
            ],
            [
                'name' => 'Atlassian Service Desk',
                'client' => 'Atlassian Corp.',
                'project_type' => 'cr',
                'current_phase' => 'implementacija_testiranje',
                'overall_health' => 'on_track',
                'estimated_hours' => 180,
                'spent_hours' => 90,
                'remaining_hours' => 80,
                'project_start' => now()->subMonths(2),
                'planned_go_live' => now()->addMonths(1)->addDays(10),
                'team_lead' => 'James Miller',
                'filled_by' => 'James Miller',
                'reviewed_by' => 'Sarah Chen',
            ],
            [
                'name' => 'Maersk Inventory Hub',
                'client' => 'A.P. Møller–Maersk',
                'project_type' => 'new',
                'current_phase' => 'integracije',
                'overall_health' => 'off_track',
                'estimated_hours' => 650,
                'spent_hours' => 600,
                'remaining_hours' => 200,
                'project_start' => now()->subMonths(8),
                'planned_go_live' => now()->subDays(15),
                'team_lead' => 'Tom Weber',
                'estimation_comment' => 'Legacy system has undocumented APIs — reverse engineering taking longer than planned.',
                'filled_by' => 'Tom Weber',
                'reviewed_by' => 'Sarah Chen',
                'product_notification_description' => '3 integration endpoints are unstable — fallback mechanism needed.',
                'product_notification_duration' => '10–15 business days',
                'product_notification_deadline' => now()->addDays(5),
            ],
            [
                'name' => 'IKEA POS Refresh',
                'client' => 'IKEA Group',
                'project_type' => 'cr',
                'current_phase' => 'funkcionalna_specifikacija',
                'overall_health' => 'on_track',
                'estimated_hours' => 200,
                'spent_hours' => 50,
                'remaining_hours' => 140,
                'project_start' => now()->subMonths(1),
                'planned_go_live' => now()->addMonths(3),
                'team_lead' => 'Sarah Chen',
                'filled_by' => 'Sarah Chen',
                'reviewed_by' => 'Priya Sharma',
            ],
            [
                'name' => 'Deutsche Telekom Self-Service',
                'client' => 'Deutsche Telekom AG',
                'project_type' => 'new',
                'current_phase' => 'uat_edukacija',
                'overall_health' => 'on_track',
                'estimated_hours' => 420,
                'spent_hours' => 380,
                'remaining_hours' => 30,
                'project_start' => now()->subMonths(5),
                'planned_go_live' => now()->addDays(6),
                'team_lead' => 'Priya Sharma',
                'filled_by' => 'Priya Sharma',
                'reviewed_by' => 'Sarah Chen',
            ],
            [
                'name' => 'N26 Digital Banking',
                'client' => 'N26 GmbH',
                'project_type' => 'new',
                'current_phase' => 'implementacija_testiranje',
                'overall_health' => 'at_risk',
                'estimated_hours' => 900,
                'spent_hours' => 450,
                'remaining_hours' => 500,
                'project_start' => now()->subMonths(5),
                'planned_go_live' => now()->addMonths(3),
                'team_lead' => 'James Miller',
                'estimation_comment' => 'PSD2 compliance requirements added ~150h of unplanned work.',
                'filled_by' => 'James Miller',
                'reviewed_by' => 'Sarah Chen',
            ],
            [
                'name' => 'Bosch BI Dashboard',
                'client' => 'Bosch GmbH',
                'project_type' => 'new',
                'current_phase' => 'go_live',
                'overall_health' => 'on_track',
                'estimated_hours' => 280,
                'spent_hours' => 265,
                'remaining_hours' => 10,
                'project_start' => now()->subMonths(4),
                'planned_go_live' => now()->addDays(1),
                'team_lead' => 'Tom Weber',
                'filled_by' => 'Tom Weber',
                'reviewed_by' => 'Sarah Chen',
                'version' => 'v2.0',
            ],
        ];

        $riskPool = [
            ['Client delayed sign-off on functional spec', 'high', 'Escalate to steering committee'],
            ['Insufficient dev team capacity', 'medium', 'Engage external consultant'],
            ['Regulatory changes mid-project', 'high', 'Monitor changes, add buffer to plan'],
            ['Unstable test environment', 'medium', 'Provision cloud-based fallback environment'],
            ['Incomplete legacy system documentation', 'high', 'Reverse engineering + knowledge transfer sessions'],
            ['Dependency on third-party API', 'medium', 'Mock service + SLA contract'],
            ['Team member rotation', 'low', 'Documentation + pair programming'],
            ['Hardware delivery delay', 'medium', 'Temporary cloud-based workaround'],
            ['Client-driven scope creep', 'high', 'Strict change request process'],
            ['Production performance issues', 'medium', 'Load testing during UAT phase'],
        ];

        $stepPool = [
            'Finalise functional specification',
            'Code review for sprints 3–5',
            'Prepare UAT test scenarios',
            'Organise end-user training sessions',
            'Configure production environment',
            'Run load testing',
            'Prepare go-live checklist',
            'Schedule sign-off meeting with client',
            'Migrate data from legacy system',
            'Document API integrations',
            'Conduct security audit',
            'Set up monitoring and alerting',
            'Draft disaster recovery plan',
            'Close open bugs from QA',
            'Prepare release notes',
        ];

        foreach ($projects as $i => $data) {
            $user = $users[$i % $users->count()];

            $project = Project::create(array_merge($data, [
                'created_by'      => $user->id,
                'updated_by'      => $user->id,
                'report_date'     => now(),
                'version'         => $data['version'] ?? 'v1.0',
                'project_type_id' => $typeMap[$data['project_type']] ?? null,
            ]));

            $phaseIndex = array_search($data['current_phase'], array_keys(\App\Models\Project::$phaseLabels));
            foreach (ProjectPhase::$defaultPhases as $j => $phase) {
                $status = 'pending';
                $completionDate = null;

                if ($j < $phaseIndex) {
                    $status = 'done';
                    $completionDate = now()->subWeeks($phaseIndex - $j);
                } elseif ($j === $phaseIndex) {
                    $status = 'in_progress';
                    if ($data['overall_health'] === 'off_track' && $j > 0 && rand(0, 1)) {
                        $status = 'blocked';
                    }
                }

                $project->phases()->create([
                    'phase_name'          => $phase['phase_name'],
                    'key_activities'      => $phase['key_activities'],
                    'client_confirmation' => $phase['client_confirmation'],
                    'status'              => $status,
                    'completion_date'     => $completionDate,
                    'notes'               => $status === 'blocked' ? 'Waiting for client feedback' : ($status === 'done' ? 'Completed on schedule' : ''),
                    'sort_order'          => $phase['sort_order'],
                ]);
            }

            $numRisks = rand(1, 3);
            $selectedRisks = collect($riskPool)->random($numRisks);
            foreach ($selectedRisks as $k => $risk) {
                $project->risks()->create([
                    'description' => $risk[0],
                    'level'       => $risk[1],
                    'mitigation'  => $risk[2],
                    'sort_order'  => $k + 1,
                ]);
            }

            $numSteps = rand(2, 4);
            $selectedSteps = collect($stepPool)->random($numSteps);
            foreach ($selectedSteps as $k => $step) {
                $project->nextSteps()->create([
                    'description'  => $step,
                    'is_completed' => $k === 0 && rand(0, 1),
                    'sort_order'   => $k + 1,
                ]);
            }

            $numSnapshots = rand(5, 8);
            $notes = [
                'Initial entry',
                'Weekly update',
                'Update after steering committee',
                'Status review',
                'Post-sprint update',
                'Bi-weekly review',
                'Pre-UAT status update',
            ];

            $finalSpent     = $project->spent_hours ?? 0;
            $finalRemaining = $project->remaining_hours ?? 0;
            $finalHealth    = $project->overall_health;
            $healthPool     = ['on_track', 'at_risk', 'off_track'];

            $snapshotStates = [];
            $spent     = $finalSpent;
            $remaining = $finalRemaining;
            $health    = $finalHealth;
            for ($s = $numSnapshots - 1; $s >= 0; $s--) {
                $snapshotStates[$s] = ['spent' => max(0, $spent), 'remaining' => max(0, $remaining), 'health' => $health];
                $spent     -= rand(15, 40);
                $remaining += rand(5, 20);
                if (rand(0, 4) === 0) {
                    $health = $healthPool[array_rand($healthPool)];
                }
            }

            ksort($snapshotStates);
            foreach ($snapshotStates as $s => $state) {
                $project->spent_hours     = $state['spent'];
                $project->remaining_hours = $state['remaining'];
                $project->overall_health  = $state['health'];
                $project->save();

                $snapshot = $project->createSnapshot($user->id, $notes[array_rand($notes)]);
                $weeksAgo = $numSnapshots - $s;
                $snapshot->update(['created_at' => now()->subWeeks($weeksAgo)]);
            }

            $project->spent_hours     = $finalSpent;
            $project->remaining_hours = $finalRemaining;
            $project->overall_health  = $finalHealth;
            $project->save();

            if (rand(0, 1) === 0) {
                $commentPool = [
                    'Client confirmed the timeline in yesterday\'s call.',
                    'Need to schedule a new kick-off for the next sprint.',
                    'QA found 3 blocking bugs — resolving today.',
                    'Demo went well, positive client feedback.',
                    'Third-party API integration confirmed and working.',
                    'Escalated resource request to management.',
                    'Documentation updated in Confluence.',
                    'Planning code freeze next week.',
                ];
                $numComments = rand(1, 3);
                foreach (collect($commentPool)->random($numComments) as $k => $body) {
                    ProjectComment::create([
                        'project_id' => $project->id,
                        'user_id'    => $user->id,
                        'body'       => $body,
                        'created_at' => now()->subDays(rand(1, 14)),
                    ]);
                }
            }

            $this->createAlertNotifications($project, $user);
        }
    }

    private function createAlertNotifications(Project $project, User $user): void
    {
        $alerts = [];

        if (in_array($project->overall_health, ['at_risk', 'off_track'])) {
            $alerts[] = $project->overall_health === 'off_track' ? 'budget_overrun' : 'health_changed';
        }

        if ($project->planned_go_live && $project->planned_go_live->isBetween(now(), now()->addDays(7))) {
            $alerts[] = 'go_live_soon';
        }

        foreach ($alerts as $alertType) {
            $notification = new ProjectAlertNotification($project, $alertType);
            \DB::table('notifications')->insert([
                'id'              => (string) \Illuminate\Support\Str::uuid(),
                'type'            => ProjectAlertNotification::class,
                'notifiable_type' => User::class,
                'notifiable_id'   => $user->id,
                'data'            => json_encode($notification->toArray($user)),
                'read_at'         => null,
                'created_at'      => now()->subDays(rand(0, 5)),
                'updated_at'      => now(),
            ]);
        }
    }
}
