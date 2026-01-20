<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $table = 'games';

    protected $fillable = [
        'number',
        'date',
        'time',
        'status_id',
        'points_home',
        'points_away',
        'court_id',
        'team_id_winner',
        'team_id_home',
        'team_id_away',
        'tournament_id',
        'group_id',
        'round_id',
        'round_number',
        'round_name',
        'match_number',
        'position',
        'side',
        'games_between',
        'home_wins',
        'away_wins',
        'parent_match_1_id',
        'parent_match_2_id',
        'group_position',
        'is_final',
        'is_deleted',
        'season_sport_id',
        'original_term_date',
        'pin_code',
        'penalty_status_id',
        'home_key',
        'away_key',
        'pool_points_fixed_home',
        'pool_points_fixed_away',
        'pool_bonus_fixed_home',
        'pool_bonus_fixed_away',
    ];

    protected $casts = [
        'date' => 'date',
        'original_term_date' => 'date',
        'is_deleted' => 'boolean',
        'status_id' => 'integer',
        'points_home' => 'integer',
        'points_away' => 'integer',
        'court_id' => 'integer',
        'team_id_winner' => 'integer',
        'team_id_home' => 'integer',
        'team_id_away' => 'integer',
        'tournament_id' => 'integer',
        'group_id' => 'integer',
        'round_id' => 'integer',
        'round_number' => 'integer',
        'match_number' => 'integer',
        'position' => 'integer',
        'games_between' => 'integer',
        'home_wins' => 'integer',
        'away_wins' => 'integer',
        'parent_match_1_id' => 'integer',
        'parent_match_2_id' => 'integer',
        'group_position' => 'integer',
        'is_final' => 'boolean',
        'season_sport_id' => 'integer',
        'pin_code' => 'integer',
        'penalty_status_id' => 'integer',
        'home_key' => 'integer',
        'away_key' => 'integer',
        'pool_points_fixed_home' => 'integer',
        'pool_points_fixed_away' => 'integer',
        'pool_bonus_fixed_home' => 'integer',
        'pool_bonus_fixed_away' => 'integer',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }

    public function round()
    {
        return $this->belongsTo(Round::class, 'round_id');
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'team_id_home');
    }

    public function guestTeam()
    {
        return $this->belongsTo(Team::class, 'team_id_away');
    }

    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id');
    }

    public function conflict()
    {
        return $this->hasOne(Conflict::class, 'game_id');
    }

    public function suggestion()
    {
        return $this->hasOne(Suggestion::class, 'game_id')
                    ->whereNull('rejected_by');
    }

    public function suggestions()
    {
        return $this->hasMany(Suggestion::class, 'game_id');
    }

    public function gamePlans()
    {
        return $this->hasMany(GamePlan::class, 'game_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'to_id');
    }

    public function penalties()
    {
        return $this->hasMany(GamePenalty::class, 'game_id');
    }

    public function notes()
    {
        return $this->hasMany(GameNote::class, 'game_id');
    }

    public function gameNotes()
    {
        return $this->hasMany(GameNote::class, 'game_id');
    }

    public function winnerTeam()
    {
        return $this->belongsTo(Team::class, 'team_id_winner');
    }

    public function parentMatch1()
    {
        return $this->belongsTo(Game::class, 'parent_match_1_id');
    }

    public function parentMatch2()
    {
        return $this->belongsTo(Game::class, 'parent_match_2_id');
    }

    public function childMatches()
    {
        return $this->hasMany(Game::class, 'parent_match_1_id')
            ->orWhere('parent_match_2_id', $this->id);
    }

    public function tournamentGroup()
    {
        return $this->belongsTo(TournamentGroup::class, 'group_id');
    }
}

