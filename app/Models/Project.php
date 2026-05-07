<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'project_start' => 'date',
            'planned_go_live' => 'date',
            'product_notification_deadline' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class)->orderBy('sort_order');
    }

    public function risks(): HasMany
    {
        return $this->hasMany(ProjectRisk::class)->orderBy('sort_order');
    }

    public function nextSteps(): HasMany
    {
        return $this->hasMany(ProjectNextStep::class)->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProjectComment::class)->orderByDesc('created_at');
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(ProjectSnapshot::class)->orderByDesc('created_at');
    }

    public function createSnapshot(int $userId, ?string $changeNote = null): ProjectSnapshot
    {
        $data = $this->load(['phases', 'risks', 'nextSteps'])->toArray();

        return $this->snapshots()->create([
            'user_id' => $userId,
            'version' => $this->version,
            'snapshot_data' => $data,
            'change_note' => $changeNote,
        ]);
    }

    public static array $phaseKeys = [
        'instalacija_analiza',
        'funkcionalna_specifikacija',
        'implementacija_testiranje',
        'integracije',
        'uat_edukacija',
        'go_live',
        'hypercare',
    ];

    public static function phaseLabel(string $key): string
    {
        return __("projects.phases.$key");
    }

    public static function getPhaseLabels(): array
    {
        return collect(self::$phaseKeys)->mapWithKeys(fn ($k) => [$k => __("projects.phases.$k")])->all();
    }

    public static function healthLabel(string $key): string
    {
        return __("projects.health_$key");
    }

    public static function getHealthLabels(): array
    {
        return collect(['on_track', 'at_risk', 'off_track'])->mapWithKeys(fn ($k) => [$k => __("projects.health_$k")])->all();
    }

    // Keep static arrays for backwards compat in code that doesn't need translation
    public static array $phaseLabels = [
        'instalacija_analiza' => 'Instalacija & Analiza',
        'funkcionalna_specifikacija' => 'Funkcionalna specifikacija',
        'implementacija_testiranje' => 'Implementacija & Testiranje',
        'integracije' => 'Integracije',
        'uat_edukacija' => 'UAT & Edukacija',
        'go_live' => 'Go-Live',
        'hypercare' => 'Hypercare',
    ];

    public static array $healthLabels = [
        'on_track' => 'On Track',
        'at_risk' => 'At Risk',
        'off_track' => 'Off Track',
    ];

}
