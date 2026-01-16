<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamTournament extends Model
{
    use HasFactory;

    protected $table = 'team_tournaments';

    public $timestamps = false;

    protected $fillable = [
        'team_id',
        'tournament_id',
        'start_points',
        'is_deleted',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'tournament_id' => 'integer',
        'start_points' => 'integer',
        'is_deleted' => 'boolean',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }
}

