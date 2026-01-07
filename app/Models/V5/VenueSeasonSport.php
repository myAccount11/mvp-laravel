<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenueSeasonSport extends Model
{
    use HasFactory;

    protected $table = 'venue_season_sport';

    public $timestamps = false;

    protected $fillable = [
        'venue_id',
        'season_sport_id',
    ];

    protected $casts = [
        'venue_id' => 'integer',
        'season_sport_id' => 'integer',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function seasonSport()
    {
        return $this->belongsTo(SeasonSport::class, 'season_sport_id');
    }
}

