<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    use HasFactory;

    protected $table = 'system';

    public $timestamps = false;

    protected $fillable = [
        'season_sport_id',
        'next_coach_license',
    ];

    protected $casts = [
        'season_sport_id' => 'integer',
        'next_coach_license' => 'integer',
    ];

    public function seasonSport()
    {
        return $this->belongsTo(SeasonSport::class, 'season_sport_id');
    }
}

