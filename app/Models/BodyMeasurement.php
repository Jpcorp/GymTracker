<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id', 'recorded_at', 'waist_cm', 'hips_cm', 'chest_cm',
    'right_arm_cm', 'left_arm_cm', 'right_thigh_cm', 'left_thigh_cm', 'evaluation_id',
])]
class BodyMeasurement extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'recorded_at' => 'date',
        ];
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
