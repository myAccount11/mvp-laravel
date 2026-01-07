<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Model;

class TournamentType extends Model
{
    protected $table = 'tournament_types';
    
    public $timestamps = false;

    protected $fillable = [
        'name',
        'season_sport_id',
    ];

    protected $casts = [
        'season_sport_id' => 'integer',
    ];
}

