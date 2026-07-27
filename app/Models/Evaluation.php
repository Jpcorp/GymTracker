<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'client_id', 'evaluation_number', 'period_start', 'period_end',
    'evaluated_at', 'achievements_summary', 'trainer_notes',
])]
class Evaluation extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'evaluated_at' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function physicalMetrics(): HasMany
    {
        return $this->hasMany(PhysicalMetric::class);
    }

    public function bodyMeasurements(): HasMany
    {
        return $this->hasMany(BodyMeasurement::class);
    }

    public function bodyPhotos(): HasMany
    {
        return $this->hasMany(BodyPhoto::class);
    }
}
