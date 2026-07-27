<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'routine_id', 'name', 'muscle_group', 'sets', 'reps_range', 'rest_seconds', 'notes',
])]
class Exercise extends Model
{
    use HasFactory;

    public function routine(): BelongsTo
    {
        return $this->belongsTo(Routine::class);
    }

    public function workoutLogs(): HasMany
    {
        return $this->hasMany(WorkoutLog::class);
    }
}
