<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id', 'survey_date', 'overall_satisfaction', 'trainer_satisfaction',
    'facilities_satisfaction', 'routines_satisfaction', 'comments',
])]
class SatisfactionSurvey extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'survey_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
