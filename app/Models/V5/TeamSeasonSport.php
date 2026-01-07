<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamSeasonSport extends Model
{
    use HasFactory;

    protected $table = 'team_season_sports';

    protected $fillable = [
        'team_id',
        'season_sport_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function seasonSport()
    {
        return $this->belongsTo(SeasonSport::class, 'season_sport_id');
    }
}

