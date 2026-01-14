<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentConfig extends Model
{
    use HasFactory;

    protected $table = 'tournament_configs';

    protected $fillable = [
        'name',
        'free_reschedule_until_date',
        'registration_dead_line',
        'minimum_warmup_minutes',
        'expected_duration_minutes',
        'season_sport_id',
        'information',
        'earliest_start',
        'latest_start',
    ];

    protected $casts = [
        'free_reschedule_until_date' => 'date',
        'registration_dead_line' => 'date',
        'minimum_warmup_minutes' => 'integer',
        'expected_duration_minutes' => 'integer',
        'season_sport_id' => 'integer',
    ];
}

