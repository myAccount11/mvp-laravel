<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubSeasonSport extends Model
{
    use HasFactory;

    protected $table = 'club_season_sports';

    protected $fillable = [
        'club_id',
        'season_sport_id',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function seasonSport()
    {
        return $this->belongsTo(SeasonSport::class, 'season_sport_id');
    }
}

