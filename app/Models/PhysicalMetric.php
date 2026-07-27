<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id', 'recorded_at', 'weight_kg', 'height_cm', 'body_fat_percentage',
    'bmi', 'metabolic_age', 'basal_kcal', 'visceral_fat', 'evaluation_id',
])]
class PhysicalMetric extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'recorded_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (PhysicalMetric $metric) {
            if ($metric->weight_kg && $metric->height_cm) {
                $heightMeters = $metric->height_cm / 100;
                $metric->bmi = round($metric->weight_kg / ($heightMeters ** 2), 2);
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }
}
