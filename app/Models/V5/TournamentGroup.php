<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentGroup extends Model
{
    use HasFactory;

    protected $table = 'tournament_groups';

    protected $fillable = [
        'tournament_id',
        'name',
        'group_number',
        'teams_count',
        'games_between',
        'advancing_teams_count',
        'is_deleted',
    ];

    protected $casts = [
        'tournament_id' => 'integer',
        'group_number' => 'integer',
        'teams_count' => 'integer',
        'games_between' => 'integer',
        'advancing_teams_count' => 'integer',
        'is_deleted' => 'boolean',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_tournament_groups', 'group_id', 'team_id')
            ->withPivot('position', 'points', 'wins', 'losses', 'draws', 'goals_for', 'goals_against')
            ->orderBy('pivot_position');
    }

    public function matches()
    {
        // Group matches are games with group_id set
        return $this->hasMany(Game::class, 'group_id');
    }
}
