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

    public function seasonSport()
    {
        return $this->belongsTo(SeasonSport::class, 'season_sport_id');
    }

    public function tournamentGroups()
    {
        return $this->hasMany(TournamentGroup::class, 'tournament_type_id');
    }
}

