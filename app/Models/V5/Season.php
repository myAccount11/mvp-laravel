<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $table = 'seasons';

    protected $fillable = [
        'name',
    ];

    public function seasonSports()
    {
        return $this->hasMany(SeasonSport::class, 'season_id');
    }

    public function sports()
    {
        return $this->belongsToMany(Sport::class, 'season_sports', 'season_id', 'sport_id');
    }
}

