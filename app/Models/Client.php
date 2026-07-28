<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

#[Fillable([
    'name', 'email', 'phone', 'birth_date', 'gender', 'start_date',
    'goal', 'profile_photo', 'status', 'trainer_id',
    'goal_metric', 'goal_target_value', 'goal_target_date',
    'nutrition_target_kcal', 'nutrition_target_protein_g', 'nutrition_target_notes',
])]
class Client extends Model
{
    use HasFactory, SoftDeletes;

    private const DELOAD_INTERVAL_WEEKS = 6;

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'start_date' => 'date',
            'goal_target_date' => 'date',
        ];
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function physicalMetrics(): HasMany
    {
        return $this->hasMany(PhysicalMetric::class);
    }

    public function bodyMeasurements(): HasMany
    {
        return $this->hasMany(BodyMeasurement::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function routines(): HasMany
    {
        return $this->hasMany(Routine::class);
    }

    public function workoutLogs(): HasMany
    {
        return $this->hasMany(WorkoutLog::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function moodRecords(): HasMany
    {
        return $this->hasMany(MoodRecord::class);
    }

    public function injuries(): HasMany
    {
        return $this->hasMany(Injury::class);
    }

    /**
     * Count of injuries not yet marked resolved — surfaced as a warning badge so a trainer
     * sees it before assigning load, closing the loop between the ACWR risk chart and actual
     * injury outcomes (per athlete-monitoring/injury-surveillance practice).
     */
    public function activeInjuriesCount(): int
    {
        return $this->injuries()->where('status', '!=', 'resolved')->count();
    }

    public function nutritionLogs(): HasMany
    {
        return $this->hasMany(NutritionLog::class);
    }

    public function satisfactionSurveys(): HasMany
    {
        return $this->hasMany(SatisfactionSurvey::class);
    }

    public function mobilityAssessments(): HasMany
    {
        return $this->hasMany(MobilityAssessment::class);
    }

    public function bodyPhotos(): HasMany
    {
        return $this->hasMany(BodyPhoto::class);
    }

    private array $comparableFields = [
        'weight_kg', 'body_fat_percentage', 'bmi', 'basal_kcal', 'visceral_fat',
    ];

    /**
     * Each evaluation's most recent physical metric, compared field-by-field against
     * the previous evaluation's metric (skipping fields null in either period).
     */
    public function evaluationsWithComparison(): \Illuminate\Support\Collection
    {
        $evaluations = $this->evaluations()->orderBy('evaluation_number')->get();

        $previousMetric = null;

        return $evaluations->map(function (Evaluation $evaluation) use (&$previousMetric) {
            $metric = $evaluation->physicalMetrics()->latest('recorded_at')->first();

            $comparison = [];
            foreach ($this->comparableFields as $field) {
                if ($metric?->$field === null || $previousMetric?->$field === null) {
                    continue;
                }

                $comparison[$field] = [
                    'current' => $metric->$field,
                    'previous' => $previousMetric->$field,
                    'delta' => round((float) $metric->$field - (float) $previousMetric->$field, 2),
                ];
            }

            $row = [
                'evaluation' => $evaluation,
                'metric' => $metric,
                'has_previous' => $previousMetric !== null,
                'comparison' => $comparison,
                'achievements' => $this->computeAchievements($evaluation),
            ];

            if ($metric) {
                $previousMetric = $metric;
            }

            return $row;
        });
    }

    /**
     * "Logros y hitos del período" (RF 4.8): workout records, attendance %, nutrition
     * compliance % for the given evaluation's period_start..period_end window.
     */
    private function computeAchievements(Evaluation $evaluation): array
    {
        $periodStart = $evaluation->period_start;
        $periodEnd = $evaluation->period_end;
        $achievements = [];

        $logsInPeriod = $this->workoutLogs()
            ->whereBetween('workout_date', [$periodStart, $periodEnd])
            ->with('exercise')
            ->get()
            ->groupBy('exercise_id');

        foreach ($logsInPeriod as $exerciseId => $logs) {
            $currentMax = (float) $logs->max('weight_kg');
            $priorMax = $this->workoutLogs()
                ->where('exercise_id', $exerciseId)
                ->where('workout_date', '<', $periodStart)
                ->max('weight_kg');

            if ($priorMax !== null && $currentMax > (float) $priorMax) {
                $delta = round($currentMax - (float) $priorMax, 2);
                $achievements[] = "Nuevo récord en {$logs->first()->exercise->name}: {$currentMax} kg (+{$delta} kg)";
            }
        }

        $days = $periodStart->diffInDays($periodEnd) + 1;
        $attendanceCount = $this->attendances()
            ->whereBetween('attendance_date', [$periodStart, $periodEnd])
            ->distinct()
            ->count('attendance_date');

        if ($attendanceCount > 0 && $days > 0) {
            $pct = (int) round($attendanceCount / $days * 100);
            $achievements[] = "Asistencia del {$pct}% ({$attendanceCount} de {$days} días posibles)";
        }

        $nutritionLogs = $this->nutritionLogs()
            ->whereBetween('log_date', [$periodStart, $periodEnd])
            ->whereNotNull('meals_planned')
            ->where('meals_planned', '>', 0)
            ->get();

        if ($nutritionLogs->isNotEmpty()) {
            $avgPct = $nutritionLogs->avg(fn ($log) => $log->meals_logged / $log->meals_planned) * 100;
            $achievements[] = 'Cumplimiento alimentario promedio: '.round($avgPct).'%';
        }

        return $achievements;
    }

    /**
     * Due date of the next 21-day evaluation: 21 days after the last evaluation's
     * period_end, or after start_date if no evaluation exists yet.
     */
    public function nextEvaluationDate(): Carbon
    {
        $last = $this->evaluations()->orderByDesc('evaluation_number')->first();
        $anchor = $last?->period_end ?? $this->start_date;

        return $anchor->copy()->addDays(21);
    }

    /**
     * Days remaining until the next evaluation is due; 0 or negative means due/overdue.
     */
    public function daysUntilNextEvaluation(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->nextEvaluationDate()->copy()->startOfDay(), false);
    }

    /**
     * Distinct attendance days this month / calendar days elapsed so far this month, rounded to a whole percent.
     */
    public function monthlyAttendancePercentage(): int
    {
        $now = now();

        $distinctDays = $this->attendances()
            ->whereBetween('attendance_date', [$now->copy()->startOfMonth(), $now->copy()->endOfDay()])
            ->distinct()
            ->count('attendance_date');

        return (int) round($distinctDays / $now->day * 100);
    }

    /**
     * SMART goal progress: baseline (earliest recorded value) vs current (latest) vs
     * target for `goal_metric`, as a 0-100 clamped percent. Null if no goal is set or
     * no PhysicalMetric data exists yet for the chosen metric.
     */
    public function goalProgress(): ?array
    {
        if (! $this->goal_metric || $this->goal_target_value === null) {
            return null;
        }

        $field = $this->goal_metric;

        $baselineMetric = $this->physicalMetrics()->whereNotNull($field)->orderBy('recorded_at')->first();
        $currentMetric = $this->physicalMetrics()->whereNotNull($field)->orderByDesc('recorded_at')->first();

        if (! $baselineMetric || ! $currentMetric) {
            return null;
        }

        $baseline = (float) $baselineMetric->$field;
        $current = (float) $currentMetric->$field;
        $target = (float) $this->goal_target_value;

        if ($target == $baseline) {
            $percent = $current >= $target ? 100 : 0;
        } else {
            $percent = (int) round((($current - $baseline) / ($target - $baseline)) * 100);
        }

        $percent = max(0, min(100, $percent));

        return [
            'metric' => $this->goal_metric,
            'baseline' => $baseline,
            'current' => $current,
            'target' => $target,
            'percent' => $percent,
            'target_date' => $this->goal_target_date,
            'days_remaining' => $this->goal_target_date
                ? (int) now()->startOfDay()->diffInDays($this->goal_target_date->copy()->startOfDay(), false)
                : null,
        ];
    }

    /**
     * True when the client has gone DELOAD_INTERVAL_WEEKS or more without a deload-phase routine,
     * counted from their earliest active-training routine (or their most recent deload's end, if any).
     */
    public function deloadRecommended(): bool
    {
        if ($this->routines()->count() === 0) {
            return false;
        }

        $lastDeload = $this->routines()->where('phase', 'deload')->orderByDesc('start_date')->first();

        $anchor = $lastDeload
            ? ($lastDeload->end_date ?? $lastDeload->start_date)
            : $this->routines()->orderBy('start_date')->first()->start_date;

        return $anchor->diffInWeeks(now()) >= self::DELOAD_INTERVAL_WEEKS;
    }

    private const RETURN_RAMP_GAP_DAYS = 7;

    /**
     * True when the client's two most recent attendances are separated by RETURN_RAMP_GAP_DAYS or
     * more, AND the most recent one is within the last 3 days — i.e. they just came back from a
     * silence long enough to have detrained, so load should ramp back up gradually rather than
     * resuming at the same volume (avoids an ACWR spike right after a comeback).
     */
    public function returnRampRecommended(): bool
    {
        $lastTwo = $this->attendances()
            ->orderByDesc('attendance_date')
            ->distinct()
            ->limit(2)
            ->pluck('attendance_date');

        if ($lastTwo->count() < 2) {
            return false;
        }

        [$mostRecent, $secondMostRecent] = $lastTwo;

        return abs($mostRecent->diffInDays($secondMostRecent)) >= self::RETURN_RAMP_GAP_DAYS
            && abs(now()->diffInDays($mostRecent)) <= 3;
    }
}
