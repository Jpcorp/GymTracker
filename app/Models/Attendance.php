<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id', 'attendance_date', 'check_in', 'check_out', 'session_type', 'duration_minutes',
])]
class Attendance extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
