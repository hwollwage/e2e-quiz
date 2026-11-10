<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressLog extends Model
{
    // mass assignment fields
    protected $fillable = [
        'user_id', 
        'log_date', 
        'weight_kg', 
        'muscle_mass_kg', 
        'body_fat_pct', 
        'workout_notes'
    ];

    // conv. log_date into Date object
    protected $casts = [
        'log_date' => 'date',
    ];

    // rel: progressLog -> User
    // each progress log belongs to exactly 1 user
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
