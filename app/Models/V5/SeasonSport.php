<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonSport extends Model
{
    use HasFactory;

    protected $table = 'season_sports';

    protected $fillable = [
        'season_id',
        'sport_id',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_season_sports', 'season_sport_id', 'user_id')
            ->withPivot('is_active');
    }
}

