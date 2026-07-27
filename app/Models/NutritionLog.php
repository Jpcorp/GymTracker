<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id', 'log_date', 'compliance', 'meals_logged', 'meals_planned', 'notes',
])]
class NutritionLog extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'log_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
