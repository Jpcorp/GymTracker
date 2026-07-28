<?php

namespace App\Livewire\Client;

use App\Models\Client;
use App\Models\Injury;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\ImageManager;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ClientShow extends Component
{
    use WithFileUploads;

    public Client $client;

    public string $recorded_at = '';

    public ?string $weight_kg = null;

    public ?string $height_cm = null;

    public ?string $body_fat_percentage = null;

    public ?string $metabolic_age = null;

    public ?string $basal_kcal = null;

    public ?string $visceral_fat = null;

    public string $bm_recorded_at = '';

    public ?string $bm_waist_cm = null;

    public ?string $bm_hips_cm = null;

    public ?string $bm_chest_cm = null;

    public ?string $bm_right_arm_cm = null;

    public ?string $bm_left_arm_cm = null;

    public ?string $bm_right_thigh_cm = null;

    public ?string $bm_left_thigh_cm = null;

    public string $photo_date = '';

    public string $attendance_date = '';

    public ?string $check_in = null;

    public ?string $check_out = null;

    public string $session_type = 'personal';

    public int $calendarYear;

    public int $calendarMonth;

    public $front_photo = null;

    public $back_photo = null;

    public $left_side_photo = null;

    public $right_side_photo = null;

    public string $mood_week_start = '';

    public string $mood_week_end = '';

    public ?string $mood_level = null;

    public ?string $mood_energy_level = null;

    public ?string $mood_motivation_level = null;

    public ?string $mood_sleep_hours = null;

    public ?string $mood_sleep_quality = null;

    public ?string $mood_notes = null;

    public string $nutrition_log_date = '';

    public string $nutrition_compliance = 'complete';

    public ?string $nutrition_meals_logged = null;

    public ?string $nutrition_meals_planned = null;

    public ?string $nutrition_notes = null;

    public string $satisfaction_survey_date = '';

    public ?string $satisfaction_overall = null;

    public ?string $satisfaction_trainer = null;

    public ?string $satisfaction_facilities = null;

    public ?string $satisfaction_routines = null;

    public ?string $satisfaction_comments = null;

    public string $injury_body_part = '';

    public string $injury_reported_date = '';

    public ?string $injury_severity = null;

    public string $injury_status = 'active';

    public ?string $injury_notes = null;

    public function mount(Client $client): void
    {
        $this->authorize('view', $client);

        $this->client = $client;
        $this->recorded_at = now()->format('Y-m-d');
        $this->bm_recorded_at = now()->format('Y-m-d');
        $this->photo_date = now()->format('Y-m-d');
        $this->attendance_date = now()->format('Y-m-d');
        $this->calendarYear = (int) now()->year;
        $this->calendarMonth = (int) now()->month;
        $this->mood_week_start = now()->startOfWeek()->format('Y-m-d');
        $this->mood_week_end = now()->endOfWeek()->format('Y-m-d');
        $this->nutrition_log_date = now()->format('Y-m-d');
        $this->satisfaction_survey_date = now()->format('Y-m-d');
        $this->injury_reported_date = now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'recorded_at' => ['required', 'date'],
            'weight_kg' => ['required', 'numeric', 'min:0.01'],
            'height_cm' => ['required', 'numeric', 'between:50,250'],
            'body_fat_percentage' => ['nullable', 'numeric', 'between:0,100'],
            'metabolic_age' => ['nullable', 'integer', 'min:1'],
            'basal_kcal' => ['nullable', 'integer', 'min:1'],
            'visceral_fat' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function saveMetric(): void
    {
        $this->authorize('update', $this->client);

        $data = $this->validate();

        $this->client->physicalMetrics()->create($data);

        $this->reset(['weight_kg', 'height_cm', 'body_fat_percentage', 'metabolic_age', 'basal_kcal', 'visceral_fat']);
        $this->recorded_at = now()->format('Y-m-d');
    }

    protected function bodyMeasurementRules(): array
    {
        return [
            'bm_recorded_at' => ['required', 'date'],
            'bm_waist_cm' => ['nullable', 'numeric', 'min:0'],
            'bm_hips_cm' => ['nullable', 'numeric', 'min:0'],
            'bm_chest_cm' => ['nullable', 'numeric', 'min:0'],
            'bm_right_arm_cm' => ['nullable', 'numeric', 'min:0'],
            'bm_left_arm_cm' => ['nullable', 'numeric', 'min:0'],
            'bm_right_thigh_cm' => ['nullable', 'numeric', 'min:0'],
            'bm_left_thigh_cm' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function saveBodyMeasurement(): void
    {
        $this->authorize('update', $this->client);

        $data = $this->validate($this->bodyMeasurementRules());

        $measurementFields = ['bm_waist_cm', 'bm_hips_cm', 'bm_chest_cm', 'bm_right_arm_cm', 'bm_left_arm_cm', 'bm_right_thigh_cm', 'bm_left_thigh_cm'];

        if (collect($measurementFields)->every(fn ($field) => $data[$field] === null)) {
            $this->addError('bm_waist_cm', __('clients.measurements.at_least_one'));

            return;
        }

        $this->client->bodyMeasurements()->create([
            'recorded_at' => $data['bm_recorded_at'],
            'waist_cm' => $data['bm_waist_cm'],
            'hips_cm' => $data['bm_hips_cm'],
            'chest_cm' => $data['bm_chest_cm'],
            'right_arm_cm' => $data['bm_right_arm_cm'],
            'left_arm_cm' => $data['bm_left_arm_cm'],
            'right_thigh_cm' => $data['bm_right_thigh_cm'],
            'left_thigh_cm' => $data['bm_left_thigh_cm'],
            'evaluation_id' => null,
        ]);

        $this->reset($measurementFields);
        $this->bm_recorded_at = now()->format('Y-m-d');
    }

    protected function photoRules(): array
    {
        $imageRules = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'];

        return [
            'photo_date' => ['required', 'date'],
            'front_photo' => $imageRules,
            'back_photo' => $imageRules,
            'left_side_photo' => $imageRules,
            'right_side_photo' => $imageRules,
        ];
    }

    public function uploadPhotos(): void
    {
        $this->authorize('update', $this->client);

        $data = $this->validate($this->photoRules());

        $files = [
            'front' => $this->front_photo,
            'back' => $this->back_photo,
            'left_side' => $this->left_side_photo,
            'right_side' => $this->right_side_photo,
        ];

        foreach ($files as $viewType => $file) {
            if (! $file) {
                continue;
            }

            $this->client->bodyPhotos()->create([
                'photo_date' => $data['photo_date'],
                'view_type' => $viewType,
                'photo_path' => $this->storeCompressed($file, $viewType),
                'evaluation_id' => null,
            ]);
        }

        $this->reset(['front_photo', 'back_photo', 'left_side_photo', 'right_side_photo']);
        $this->photo_date = now()->format('Y-m-d');
    }

    private function storeCompressed(UploadedFile $file, string $viewType): string
    {
        $image = (new ImageManager(new Driver()))->decodePath($file->getRealPath());
        $image->scaleDown(width: 1200);
        $encoded = $image->encode(new JpegEncoder(quality: 75));

        $path = 'body_photos/'.$this->client->id.'/'.$viewType.'-'.now()->format('YmdHis').'-'.uniqid().'.jpg';

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    protected function attendanceRules(): array
    {
        return [
            'attendance_date' => ['required', 'date'],
            'check_in' => ['nullable', 'date_format:H:i'],
            'check_out' => ['nullable', 'date_format:H:i'],
            'session_type' => ['required', 'in:personal,group,free'],
        ];
    }

    public function checkIn(): void
    {
        $this->authorize('update', $this->client);

        $data = $this->validate($this->attendanceRules());

        $this->client->attendances()->create($data);

        $this->reset(['check_in', 'check_out']);
        $this->attendance_date = now()->format('Y-m-d');
        $this->session_type = 'personal';
    }

    public function previousMonth(): void
    {
        $this->authorize('view', $this->client);

        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarYear = $date->year;
        $this->calendarMonth = $date->month;
    }

    public function nextMonth(): void
    {
        $this->authorize('view', $this->client);

        $date = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarYear = $date->year;
        $this->calendarMonth = $date->month;
    }

    /**
     * Full Mon-Sun week grid for the selected month, each day flagged with whether
     * it belongs to the current month and whether the client has an attendance row on it.
     */
    public function calendarWeeks(): array
    {
        $firstOfMonth = Carbon::create($this->calendarYear, $this->calendarMonth, 1);
        $gridStart = $firstOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $firstOfMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $attendedDays = $this->client->attendances()
            ->whereBetween('attendance_date', [$gridStart->format('Y-m-d'), $gridEnd->format('Y-m-d')])
            ->pluck('attendance_date')
            ->map(fn ($date) => $date->format('Y-m-d'))
            ->unique();

        $weeks = [];
        $cursor = $gridStart->copy();

        while ($cursor->lte($gridEnd)) {
            $week = [];

            for ($i = 0; $i < 7; $i++) {
                $week[] = [
                    'date' => $cursor->copy(),
                    'inMonth' => $cursor->month === $this->calendarMonth,
                    'isToday' => $cursor->isToday(),
                    'attended' => $attendedDays->contains($cursor->format('Y-m-d')),
                ];
                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        return $weeks;
    }

    public function calendarWeekdayLabels(): array
    {
        $start = now()->startOfWeek(Carbon::MONDAY);

        return collect(range(0, 6))->map(fn ($i) => $start->copy()->addDays($i)->translatedFormat('D'))->all();
    }

    protected function moodRules(): array
    {
        return [
            'mood_week_start' => ['required', 'date'],
            'mood_week_end' => ['required', 'date', 'after_or_equal:mood_week_start'],
            'mood_level' => ['required', 'integer', 'between:1,10'],
            'mood_energy_level' => ['nullable', 'integer', 'between:1,10'],
            'mood_motivation_level' => ['nullable', 'integer', 'between:1,10'],
            'mood_sleep_hours' => ['nullable', 'numeric', 'between:0,24'],
            'mood_sleep_quality' => ['nullable', 'integer', 'between:1,10'],
            'mood_notes' => ['nullable', 'string'],
        ];
    }

    public function saveMood(): void
    {
        $this->authorize('update', $this->client);

        $data = $this->validate($this->moodRules());

        $this->client->moodRecords()->create([
            'week_start' => $data['mood_week_start'],
            'week_end' => $data['mood_week_end'],
            'mood_level' => $data['mood_level'],
            'energy_level' => $data['mood_energy_level'],
            'motivation_level' => $data['mood_motivation_level'],
            'sleep_hours' => $data['mood_sleep_hours'],
            'sleep_quality' => $data['mood_sleep_quality'],
            'notes' => $data['mood_notes'],
        ]);

        $this->reset(['mood_level', 'mood_energy_level', 'mood_motivation_level', 'mood_sleep_hours', 'mood_sleep_quality', 'mood_notes']);
        $this->mood_week_start = now()->startOfWeek()->format('Y-m-d');
        $this->mood_week_end = now()->endOfWeek()->format('Y-m-d');
    }

    protected function nutritionRules(): array
    {
        return [
            'nutrition_log_date' => ['required', 'date'],
            'nutrition_compliance' => ['required', 'in:complete,partial,missed'],
            'nutrition_meals_logged' => ['nullable', 'integer', 'min:0'],
            'nutrition_meals_planned' => ['nullable', 'integer', 'min:0'],
            'nutrition_notes' => ['nullable', 'string'],
        ];
    }

    public function saveNutritionLog(): void
    {
        $this->authorize('update', $this->client);

        $data = $this->validate($this->nutritionRules());

        $this->client->nutritionLogs()->create([
            'log_date' => $data['nutrition_log_date'],
            'compliance' => $data['nutrition_compliance'],
            'meals_logged' => $data['nutrition_meals_logged'],
            'meals_planned' => $data['nutrition_meals_planned'],
            'notes' => $data['nutrition_notes'],
        ]);

        $this->reset(['nutrition_meals_logged', 'nutrition_meals_planned', 'nutrition_notes']);
        $this->nutrition_compliance = 'complete';
        $this->nutrition_log_date = now()->format('Y-m-d');
    }

    protected function satisfactionRules(): array
    {
        return [
            'satisfaction_survey_date' => ['required', 'date'],
            'satisfaction_overall' => ['required', 'integer', 'between:1,10'],
            'satisfaction_trainer' => ['nullable', 'integer', 'between:1,10'],
            'satisfaction_facilities' => ['nullable', 'integer', 'between:1,10'],
            'satisfaction_routines' => ['nullable', 'integer', 'between:1,10'],
            'satisfaction_comments' => ['nullable', 'string'],
        ];
    }

    public function saveSatisfactionSurvey(): void
    {
        $this->authorize('update', $this->client);

        $data = $this->validate($this->satisfactionRules());

        $this->client->satisfactionSurveys()->create([
            'survey_date' => $data['satisfaction_survey_date'],
            'overall_satisfaction' => $data['satisfaction_overall'],
            'trainer_satisfaction' => $data['satisfaction_trainer'],
            'facilities_satisfaction' => $data['satisfaction_facilities'],
            'routines_satisfaction' => $data['satisfaction_routines'],
            'comments' => $data['satisfaction_comments'],
        ]);

        $this->reset(['satisfaction_overall', 'satisfaction_trainer', 'satisfaction_facilities', 'satisfaction_routines', 'satisfaction_comments']);
        $this->satisfaction_survey_date = now()->format('Y-m-d');
    }

    protected function injuryRules(): array
    {
        return [
            'injury_body_part' => ['required', 'string', 'max:100'],
            'injury_reported_date' => ['required', 'date'],
            'injury_severity' => ['required', 'integer', 'between:1,10'],
            'injury_status' => ['required', 'in:active,recovering,resolved'],
            'injury_notes' => ['nullable', 'string'],
        ];
    }

    public function saveInjury(): void
    {
        $this->authorize('update', $this->client);

        $data = $this->validate($this->injuryRules());

        $this->client->injuries()->create([
            'body_part' => $data['injury_body_part'],
            'reported_date' => $data['injury_reported_date'],
            'severity' => $data['injury_severity'],
            'status' => $data['injury_status'],
            'notes' => $data['injury_notes'],
            'resolved_date' => $data['injury_status'] === 'resolved' ? now()->format('Y-m-d') : null,
        ]);

        $this->reset(['injury_body_part', 'injury_severity', 'injury_notes']);
        $this->injury_status = 'active';
        $this->injury_reported_date = now()->format('Y-m-d');
    }

    public function resolveInjury(Injury $injury): void
    {
        $this->authorize('update', $this->client);
        abort_unless($injury->client_id === $this->client->id, 404);

        $injury->update(['status' => 'resolved', 'resolved_date' => now()->format('Y-m-d')]);
    }

    /**
     * Distinct attendance days this month / calendar days elapsed so far this month, rounded to a whole percent.
     */
    public function monthlyAttendancePercentage(): int
    {
        return $this->client->monthlyAttendancePercentage();
    }

    public function render()
    {
        return view('livewire.client.client-show', [
            'metrics' => $this->client->physicalMetrics()->orderByDesc('recorded_at')->get(),
            'measurements' => $this->client->bodyMeasurements()->orderByDesc('recorded_at')->get(),
            'photosByDate' => $this->client->bodyPhotos()
                ->orderByDesc('photo_date')
                ->get()
                ->groupBy(fn ($photo) => $photo->photo_date->format('Y-m-d')),
            'evaluations' => $this->client->evaluationsWithComparison(),
            'chartData' => $this->progressChartData(),
            'attendances' => $this->client->attendances()->orderByDesc('attendance_date')->orderByDesc('id')->get(),
            'attendancePercentage' => $this->monthlyAttendancePercentage(),
            'calendarWeeks' => $this->calendarWeeks(),
            'calendarWeekdayLabels' => $this->calendarWeekdayLabels(),
            'calendarLabel' => Carbon::create($this->calendarYear, $this->calendarMonth, 1)->translatedFormat('F Y'),
            'moodRecords' => $this->client->moodRecords()->orderByDesc('week_start')->get(),
            'nutritionLogs' => $this->client->nutritionLogs()->orderByDesc('log_date')->get(),
            'satisfactionSurveys' => $this->client->satisfactionSurveys()->orderByDesc('survey_date')->get(),
            'moodChartData' => $this->moodChartData(),
            'nutritionChartData' => $this->nutritionChartData(),
            'acwrChartData' => $this->acwrChartData(),
            'symmetryChartData' => $this->symmetryChartData(),
            'goalProgress' => $this->client->goalProgress(),
            'deloadRecommended' => $this->client->deloadRecommended(),
            'injuries' => $this->client->injuries()->orderByDesc('reported_date')->get(),
            'activeInjuriesCount' => $this->client->activeInjuriesCount(),
        ]);
    }

    private function chartSeries(): array
    {
        return [
            'weight_kg' => __('clients.metrics.weight_kg'),
            'body_fat_percentage' => __('clients.metrics.body_fat_percentage'),
            'bmi' => __('clients.metrics.bmi'),
        ];
    }

    /**
     * Chart-ready payload for the progress line chart: dates ascending, one series
     * per metric field, skipping fields that are null across every record.
     */
    public function progressChartData(): array
    {
        $metrics = $this->client->physicalMetrics()->orderBy('recorded_at')->get();

        $labels = $metrics->map(fn ($metric) => $metric->recorded_at->format('Y-m-d'))->values()->all();

        $series = [];
        foreach ($this->chartSeries() as $field => $label) {
            if ($metrics->every(fn ($metric) => $metric->$field === null)) {
                continue;
            }

            $series[] = [
                'name' => $label,
                'data' => $metrics->map(fn ($metric) => $metric->$field !== null ? (float) $metric->$field : null)->values()->all(),
            ];
        }

        return [
            'labels' => $labels,
            'series' => $series,
            'hasEnoughData' => $metrics->count() >= 2,
        ];
    }

    private function moodChartSeries(): array
    {
        return [
            'mood_level' => __('wellness.mood.mood_level'),
            'energy_level' => __('wellness.mood.energy_level'),
            'motivation_level' => __('wellness.mood.motivation_level'),
            'sleep_hours' => __('wellness.mood.sleep_hours'),
        ];
    }

    /**
     * Chart-ready payload for the mood trend chart: weeks ascending, one series
     * per level field, skipping fields that are null across every record.
     */
    public function moodChartData(): array
    {
        $records = $this->client->moodRecords()->orderBy('week_start')->get();

        $labels = $records->map(fn ($record) => $record->week_start->format('Y-m-d'))->values()->all();

        $series = [];
        foreach ($this->moodChartSeries() as $field => $label) {
            if ($records->every(fn ($record) => $record->$field === null)) {
                continue;
            }

            $series[] = [
                'name' => $label,
                'data' => $records->map(fn ($record) => $record->$field !== null ? (float) $record->$field : null)->values()->all(),
            ];
        }

        return [
            'labels' => $labels,
            'series' => $series,
            'hasEnoughData' => $records->count() >= 2,
        ];
    }

    /**
     * Chart-ready payload for the nutrition compliance chart: a single series with a
     * 0-100 compliance percentage per log, computed from meals_logged/meals_planned
     * when available, otherwise mapped from the compliance enum.
     */
    public function nutritionChartData(): array
    {
        $logs = $this->client->nutritionLogs()->orderBy('log_date')->get();

        $complianceMap = ['complete' => 100, 'partial' => 50, 'missed' => 0];

        $labels = $logs->map(fn ($log) => $log->log_date->format('Y-m-d'))->values()->all();

        $data = $logs->map(function ($log) use ($complianceMap) {
            if ($log->meals_planned) {
                return (float) round($log->meals_logged / $log->meals_planned * 100);
            }

            return (float) $complianceMap[$log->compliance];
        })->values()->all();

        return [
            'labels' => $labels,
            'series' => [
                ['name' => __('wellness.nutrition.compliance'), 'data' => $data],
            ],
            'hasEnoughData' => $logs->count() >= 2,
        ];
    }

    /**
     * Chart-ready payload for the ACWR (Acute:Chronic Workload Ratio) chart: one value per
     * week over the trailing 12 weeks, using distinct attendance-day counts as the load proxy.
     * Acute = current week's count, chronic = rolling 4-week average (including current week).
     * Weeks where the chronic average is 0 are skipped (divide-by-zero guard).
     */
    public function acwrChartData(): array
    {
        $weeksBack = 12;
        $today = now();

        // Weekly attendance counts for the trailing 12 weeks plus the 3 prior weeks needed
        // to compute the first week's chronic (rolling 4-week) average.
        $weeklyCounts = [];
        for ($i = $weeksBack + 3 - 1; $i >= 0; $i--) {
            $weekStart = $today->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();

            $weeklyCounts[] = $this->client->attendances()
                ->whereBetween('attendance_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                ->distinct()
                ->count('attendance_date');
        }

        $labels = [];
        $data = [];

        for ($i = 3; $i < count($weeklyCounts); $i++) {
            $acute = $weeklyCounts[$i];
            $chronic = array_sum(array_slice($weeklyCounts, $i - 3, 4)) / 4;

            $weekStart = $today->copy()->subWeeks($weeksBack + 3 - 1 - $i)->startOfWeek();
            $labels[] = $weekStart->format('Y-m-d');
            $data[] = $chronic > 0 ? round($acute / $chronic, 2) : null;
        }

        return [
            'labels' => $labels,
            'series' => [
                ['name' => __('clients.chart.acwr'), 'data' => $data],
            ],
            'hasEnoughData' => collect($data)->filter(fn ($value) => $value !== null)->count() >= 1,
        ];
    }

    /**
     * Chart-ready payload for the body symmetry chart: left vs right for arm/thigh
     * measurements, taken from the client's most recent BodyMeasurement record.
     */
    public function symmetryChartData(): array
    {
        $measurement = $this->client->bodyMeasurements()->orderByDesc('recorded_at')->first();

        if (! $measurement || ($measurement->right_arm_cm === null && $measurement->left_arm_cm === null
            && $measurement->right_thigh_cm === null && $measurement->left_thigh_cm === null)) {
            return ['labels' => [], 'series' => [], 'hasEnoughData' => false];
        }

        return [
            'labels' => [__('clients.chart.arm'), __('clients.chart.thigh')],
            'type' => 'bar',
            'series' => [
                ['name' => __('clients.chart.right'), 'data' => [
                    $measurement->right_arm_cm !== null ? (float) $measurement->right_arm_cm : null,
                    $measurement->right_thigh_cm !== null ? (float) $measurement->right_thigh_cm : null,
                ]],
                ['name' => __('clients.chart.left'), 'data' => [
                    $measurement->left_arm_cm !== null ? (float) $measurement->left_arm_cm : null,
                    $measurement->left_thigh_cm !== null ? (float) $measurement->left_thigh_cm : null,
                ]],
            ],
            'hasEnoughData' => true,
        ];
    }
}
