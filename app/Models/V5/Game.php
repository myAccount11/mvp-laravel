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
        'pool_id',
        'group_id',
        'round_id',
        'is_deleted',
        'last_official_date',
        'last_official_time',
        'last_official_court_id',
        'season_sport_id',
        'original_term_date',
        'is_locked',
        'force_next_update',
        'original_home_team_id',
        'organizer_club_id',
        'organizer_team_id',
        'pin_code',
        'stats_calculated',
        'star_rating',
        'title_prefix',
        'penalty_status_id',
        'home_key',
        'away_key',
        'draft_id',
        'auto_time',
        'pool_points_fixed_home',
        'pool_points_fixed_away',
        'pool_bonus_fixed_home',
        'pool_bonus_fixed_away',
    ];

    protected $casts = [
        'date' => 'date',
        'last_official_date' => 'date',
        'original_term_date' => 'date',
        'is_deleted' => 'boolean',
        'is_locked' => 'boolean',
        'force_next_update' => 'boolean',
        'auto_time' => 'boolean',
        'status_id' => 'integer',
        'points_home' => 'integer',
        'points_away' => 'integer',
        'court_id' => 'integer',
        'team_id_winner' => 'integer',
        'team_id_home' => 'integer',
        'team_id_away' => 'integer',
        'tournament_id' => 'integer',
        'pool_id' => 'integer',
        'group_id' => 'integer',
        'round_id' => 'integer',
        'last_official_court_id' => 'integer',
        'season_sport_id' => 'integer',
        'original_home_team_id' => 'integer',
        'organizer_club_id' => 'integer',
        'organizer_team_id' => 'integer',
        'pin_code' => 'integer',
        'stats_calculated' => 'integer',
        'star_rating' => 'integer',
        'penalty_status_id' => 'integer',
        'home_key' => 'integer',
        'away_key' => 'integer',
        'draft_id' => 'integer',
        'pool_points_fixed_home' => 'integer',
        'pool_points_fixed_away' => 'integer',
        'pool_bonus_fixed_home' => 'integer',
        'pool_bonus_fixed_away' => 'integer',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }

    public function pool()
    {
        return $this->belongsTo(Pool::class, 'pool_id');
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

    public function club()
    {
        return $this->belongsTo(Club::class, 'organizer_club_id');
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
}

