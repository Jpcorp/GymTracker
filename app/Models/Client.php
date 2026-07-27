<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name', 'email', 'phone', 'birth_date', 'gender', 'start_date',
    'goal', 'profile_photo', 'status', 'trainer_id',
])]
class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'start_date' => 'date',
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

    public function nutritionLogs(): HasMany
    {
        return $this->hasMany(NutritionLog::class);
    }

    public function satisfactionSurveys(): HasMany
    {
        return $this->hasMany(SatisfactionSurvey::class);
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
            ];

            if ($metric) {
                $previousMetric = $metric;
            }

            return $row;
        });
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
}
