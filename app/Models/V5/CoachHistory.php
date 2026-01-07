<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachHistory extends Model
{
    use HasFactory;

    protected $table = 'coach_history';

    protected $fillable = [
        'team_id',
        'club_name',
        'tournament_name',
        'season_name',
        'coach_id',
        'mvp',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'coach_id' => 'integer',
        'mvp' => 'integer',
    ];

    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coach_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

