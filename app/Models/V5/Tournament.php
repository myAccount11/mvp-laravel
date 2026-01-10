<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $table = 'tournaments';

    public $timestamps = false;

    protected $fillable = [
        'alias',
        'short_name',
        'region_id',
        'start_date',
        'end_date',
        'pool_count',
        'standing_group_count',
        'cross_pool_game_count',
        'cross_standing_group_game_count',
        'round_type',
        'information',
        'tournament_group_id',
        'team_count',
        'deleted',
        'tournament_program_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'pool_count' => 'integer',
        'standing_group_count' => 'integer',
        'cross_pool_game_count' => 'integer',
        'cross_standing_group_game_count' => 'integer',
        'round_type' => 'integer',
        'tournament_group_id' => 'integer',
        'team_count' => 'integer',
        'deleted' => 'boolean',
        'tournament_program_id' => 'integer',
        'region_id' => 'integer',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function tournamentGroup()
    {
        return $this->belongsTo(TournamentGroup::class, 'tournament_group_id');
    }

    public function pools()
    {
        return $this->hasMany(Pool::class, 'tournament_id');
    }

    public function rounds()
    {
        return $this->hasMany(Round::class, 'tournament_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_tournaments', 'tournament_id', 'team_id')
            ->withPivot('pool_id', 'pool_key', 'start_points');
    }

    public function teamTournaments()
    {
        return $this->hasMany(TeamTournament::class, 'tournament_id');
    }
}
