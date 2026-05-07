<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPhase extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'completion_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public static array $statusLabels = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'done' => 'Done',
        'blocked' => 'Blocked',
    ];

    public static function getStatusLabel(string $key): string
    {
        return __("projects.status_$key");
    }

    public static function getDefaultPhases(): array
    {
        $keys = \App\Models\Project::$phaseKeys;
        $phases = [];
        foreach ($keys as $i => $key) {
            $phases[] = [
                'phase_name' => __("projects.default_phases.$key.name"),
                'key_activities' => __("projects.default_phases.$key.activities"),
                'client_confirmation' => __("projects.default_phases.$key.client"),
                'sort_order' => $i + 1,
            ];
        }
        return $phases;
    }

    // Keep static array for backwards compat (tests, etc.)
    public static array $defaultPhases = [
        [
            'phase_name' => 'Instalacija & Analiza zahtjeva',
            'key_activities' => 'On-prem setup, prikupljanje zahtjeva, gap analiza',
            'client_confirmation' => 'Da',
            'sort_order' => 1,
        ],
        [
            'phase_name' => 'Funkcionalna specifikacija',
            'key_activities' => 'Izrada FuncSpec, review, sign-off klijenta',
            'client_confirmation' => 'Da - sign-off obavezan',
            'sort_order' => 2,
        ],
        [
            'phase_name' => 'Implementacija & Parametrizacija',
            'key_activities' => 'Konfiguracija sustava, interna testiranja, bugfixevi',
            'client_confirmation' => '-',
            'sort_order' => 3,
        ],
        [
            'phase_name' => 'Integracije',
            'key_activities' => 'Razvoj i testiranje integracija, interno QA',
            'client_confirmation' => 'Da - interni sign-off',
            'sort_order' => 4,
        ],
        [
            'phase_name' => 'UAT & Edukacija',
            'key_activities' => 'UAT scenariji, edukacija korisnika, adoption aktivnosti',
            'client_confirmation' => 'Da - UAT sign-off',
            'sort_order' => 5,
        ],
        [
            'phase_name' => 'Go-Live',
            'key_activities' => 'Produkcijsko puštanje, go-live checklist',
            'client_confirmation' => 'Da',
            'sort_order' => 6,
        ],
        [
            'phase_name' => 'Hypercare',
            'key_activities' => 'Podrška post go-live, zatvaranje projekta',
            'client_confirmation' => 'Da - project closure',
            'sort_order' => 7,
        ],
    ];
}
