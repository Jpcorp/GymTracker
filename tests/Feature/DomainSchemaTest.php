<?php

use Illuminate\Support\Facades\Schema;

test('all domain tables exist with their expected key columns', function () {
    $tables = [
        'clients' => ['id', 'name', 'email', 'trainer_id', 'status'],
        'evaluations' => ['id', 'client_id', 'evaluation_number', 'period_start', 'period_end'],
        'physical_metrics' => ['id', 'client_id', 'weight_kg', 'height_cm', 'bmi', 'evaluation_id'],
        'body_measurements' => ['id', 'client_id', 'waist_cm', 'evaluation_id'],
        'routines' => ['id', 'client_id', 'name', 'weekly_frequency', 'is_active'],
        'exercises' => ['id', 'routine_id', 'name', 'sets', 'reps_range'],
        'workout_logs' => ['id', 'client_id', 'exercise_id', 'workout_date'],
        'attendances' => ['id', 'client_id', 'attendance_date', 'session_type'],
        'mood_records' => ['id', 'client_id', 'week_start', 'week_end', 'mood_level'],
        'nutrition_logs' => ['id', 'client_id', 'log_date', 'compliance'],
        'satisfaction_surveys' => ['id', 'client_id', 'survey_date', 'overall_satisfaction'],
        'body_photos' => ['id', 'client_id', 'photo_date', 'view_type', 'photo_path', 'evaluation_id'],
    ];

    foreach ($tables as $table => $columns) {
        expect(Schema::hasTable($table))->toBeTrue("Expected table [{$table}] to exist");
        expect(Schema::hasColumns($table, $columns))
            ->toBeTrue("Expected table [{$table}] to have columns: ".implode(', ', $columns));
    }
});
