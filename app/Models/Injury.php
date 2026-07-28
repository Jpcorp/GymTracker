<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id', 'body_part', 'reported_date', 'severity', 'status', 'notes', 'resolved_date',
])]
class Injury extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'reported_date' => 'date',
            'resolved_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
