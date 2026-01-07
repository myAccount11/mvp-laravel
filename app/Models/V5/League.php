<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    protected $table = 'leagues';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'club_id',
        'deleted',
        'season_sport_id',
        'is_active',
        'user_id',
        'information',
        'organizer_id',
        'sport_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'club_id' => 'integer',
        'deleted' => 'boolean',
        'season_sport_id' => 'integer',
        'is_active' => 'boolean',
        'user_id' => 'integer',
        'organizer_id' => 'integer',
        'sport_id' => 'integer',
    ];

    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organizer_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}

