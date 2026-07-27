<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id', 'exercise_id', 'workout_date', 'weight_kg',
    'completed_sets', 'completed_reps', 'rpe', 'notes',
])]
class WorkoutLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'workout_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
