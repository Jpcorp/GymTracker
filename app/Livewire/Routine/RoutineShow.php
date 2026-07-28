<?php

namespace App\Livewire\Routine;

use App\Models\Client;
use App\Models\Exercise;
use App\Models\Routine;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RoutineShow extends Component
{
    public Client $client;

    public Routine $routine;

    // Exercise form (shared by create + edit)
    public ?int $editingExerciseId = null;

    public string $exercise_name = '';

    public ?string $exercise_muscle_group = null;

    public ?string $exercise_sets = null;

    public ?string $exercise_reps_range = null;

    public ?string $exercise_rest_seconds = null;

    public ?string $exercise_notes = null;

    // Workout log quick-entry form, opened for one exercise at a time
    public ?int $loggingExerciseId = null;

    public string $workout_date = '';

    public ?string $weight_kg = null;

    public ?string $completed_sets = null;

    public ?string $completed_reps = null;

    public ?string $rpe = null;

    public ?string $notes = null;

    public function mount(Client $client, Routine $routine): void
    {
        $this->authorize('view', $client);
        abort_unless($routine->client_id === $client->id, 404);

        $this->client = $client;
        $this->routine = $routine;
        $this->workout_date = now()->format('Y-m-d');
    }

    protected function exerciseRules(): array
    {
        return [
            'exercise_name' => ['required', 'string', 'max:100'],
            'exercise_muscle_group' => ['nullable', 'string', 'max:50'],
            'exercise_sets' => ['required', 'integer', 'min:1'],
            'exercise_reps_range' => ['required', 'string', 'max:20'],
            'exercise_rest_seconds' => ['nullable', 'integer', 'min:0'],
            'exercise_notes' => ['nullable', 'string'],
        ];
    }

    public function saveExercise(): void
    {
        $this->authorize('update', $this->client);

        $data = $this->validate($this->exerciseRules());

        $payload = [
            'name' => $data['exercise_name'],
            'muscle_group' => $data['exercise_muscle_group'],
            'sets' => $data['exercise_sets'],
            'reps_range' => $data['exercise_reps_range'],
            'rest_seconds' => $data['exercise_rest_seconds'],
            'notes' => $data['exercise_notes'],
        ];

        if ($this->editingExerciseId) {
            $exercise = $this->routine->exercises()->findOrFail($this->editingExerciseId);
            $exercise->update($payload);
        } else {
            $this->routine->exercises()->create($payload);
        }

        $this->resetExerciseForm();
    }

    public function editExercise(Exercise $exercise): void
    {
        $this->authorize('update', $this->client);
        abort_unless($exercise->routine_id === $this->routine->id, 404);

        $this->editingExerciseId = $exercise->id;
        $this->exercise_name = $exercise->name;
        $this->exercise_muscle_group = $exercise->muscle_group;
        $this->exercise_sets = (string) $exercise->sets;
        $this->exercise_reps_range = $exercise->reps_range;
        $this->exercise_rest_seconds = $exercise->rest_seconds !== null ? (string) $exercise->rest_seconds : null;
        $this->exercise_notes = $exercise->notes;
    }

    public function cancelEditExercise(): void
    {
        $this->resetExerciseForm();
    }

    public function deleteExercise(Exercise $exercise): void
    {
        $this->authorize('update', $this->client);
        abort_unless($exercise->routine_id === $this->routine->id, 404);

        $exercise->delete();

        if ($this->editingExerciseId === $exercise->id) {
            $this->resetExerciseForm();
        }
    }

    private function resetExerciseForm(): void
    {
        $this->editingExerciseId = null;
        $this->reset([
            'exercise_name', 'exercise_muscle_group', 'exercise_sets',
            'exercise_reps_range', 'exercise_rest_seconds', 'exercise_notes',
        ]);
    }

    public function startLogging(Exercise $exercise): void
    {
        $this->authorize('view', $this->client);
        abort_unless($exercise->routine_id === $this->routine->id, 404);

        $this->loggingExerciseId = $exercise->id;
        $this->workout_date = now()->format('Y-m-d');
        $this->reset(['weight_kg', 'completed_sets', 'completed_reps', 'rpe', 'notes']);
    }

    public function cancelLogging(): void
    {
        $this->loggingExerciseId = null;
    }

    protected function logRules(): array
    {
        return [
            'workout_date' => ['required', 'date'],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
            'completed_sets' => ['nullable', 'integer', 'min:0'],
            'completed_reps' => ['nullable', 'string', 'max:50'],
            'rpe' => ['nullable', 'integer', 'between:1,10'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function logWorkout(): void
    {
        $this->authorize('update', $this->client);

        $exercise = $this->routine->exercises()->findOrFail($this->loggingExerciseId);

        $data = $this->validate($this->logRules());

        $exercise->workoutLogs()->create($data + ['client_id' => $this->client->id]);

        $this->loggingExerciseId = null;
        $this->workout_date = now()->format('Y-m-d');
        $this->reset(['weight_kg', 'completed_sets', 'completed_reps', 'rpe', 'notes']);
    }

    public function render()
    {
        $exercises = $this->routine->exercises()
            ->with(['workoutLogs' => fn ($query) => $query->orderByDesc('workout_date')->orderByDesc('id')])
            ->get();

        return view('livewire.routine.routine-show', [
            'exercises' => $exercises,
            'e1rmCharts' => $exercises->mapWithKeys(fn ($exercise) => [$exercise->id => $this->e1rmChartData($exercise)]),
            'volumeChartData' => $this->volumeByMuscleGroupChartData(),
            'rpeChartData' => $this->rpeChartData(),
        ]);
    }

    /**
     * Chart-ready payload for the estimated 1RM trend of a single exercise, using the
     * Epley formula (e1RM = weight * (1 + reps/30)) against its workout log history.
     * Logs with no weight or an unparseable/zero rep count are skipped (can't estimate).
     */
    public function e1rmChartData(Exercise $exercise): array
    {
        $points = $exercise->workoutLogs
            ->filter(fn ($log) => $log->weight_kg !== null && (int) $log->completed_reps > 0)
            ->sortBy('workout_date')
            ->values();

        return [
            'labels' => $points->map(fn ($log) => $log->workout_date->format('Y-m-d'))->all(),
            'series' => [[
                'name' => __('routines.performance.e1rm_series'),
                'data' => $points->map(fn ($log) => round((float) $log->weight_kg * (1 + ((int) $log->completed_reps) / 30), 2))->all(),
            ]],
            'hasEnoughData' => $points->count() >= 2,
        ];
    }

    /**
     * Chart-ready payload for training volume (weight * sets * reps) per muscle group,
     * bucketed by ISO week, across every exercise in the current routine.
     */
    public function volumeByMuscleGroupChartData(): array
    {
        $exercises = $this->routine->exercises()->with('workoutLogs')->get();

        $entries = collect();
        foreach ($exercises as $exercise) {
            $group = $exercise->muscle_group ?: __('routines.performance.no_muscle_group');

            foreach ($exercise->workoutLogs as $log) {
                $reps = (int) $log->completed_reps;
                if ($log->weight_kg === null || $log->completed_sets === null || $reps <= 0) {
                    continue;
                }

                $entries->push([
                    'week' => $log->workout_date->copy()->startOfWeek()->format('Y-m-d'),
                    'muscle_group' => $group,
                    'volume' => (float) $log->weight_kg * $log->completed_sets * $reps,
                ]);
            }
        }

        $weeks = $entries->pluck('week')->unique()->sort()->values();
        $groups = $entries->pluck('muscle_group')->unique()->values();

        $series = $groups->map(fn ($group) => [
            'name' => $group,
            'data' => $weeks->map(fn ($week) => (float) $entries
                ->where('week', $week)
                ->where('muscle_group', $group)
                ->sum('volume'))->all(),
        ])->values()->all();

        return [
            'labels' => $weeks->map(fn ($week) => __('routines.performance.week_label', ['date' => $week]))->all(),
            'series' => $series,
            'hasEnoughData' => $entries->isNotEmpty(),
            'type' => 'bar',
        ];
    }

    /**
     * Chart-ready payload for RPE over time across the current routine's workout logs,
     * averaged per day when multiple logs (from different exercises) share a date.
     */
    public function rpeChartData(): array
    {
        $exercises = $this->routine->exercises()->with('workoutLogs')->get();

        $byDate = $exercises
            ->flatMap(fn ($exercise) => $exercise->workoutLogs)
            ->filter(fn ($log) => $log->rpe !== null)
            ->groupBy(fn ($log) => $log->workout_date->format('Y-m-d'))
            ->map(fn ($logs) => round((float) $logs->avg('rpe'), 1))
            ->sortKeys();

        return [
            'labels' => $byDate->keys()->all(),
            'series' => [[
                'name' => __('routines.performance.rpe_series'),
                'data' => $byDate->values()->all(),
            ]],
            'hasEnoughData' => $byDate->count() >= 2,
        ];
    }
}
