<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id', 'photo_date', 'view_type', 'photo_path', 'evaluation_id', 'notes',
])]
class BodyPhoto extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'photo_date' => 'date',
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
