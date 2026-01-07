<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentGroup extends Model
{
    use HasFactory;

    protected $table = 'tournament_groups';

    protected $fillable = [
        'name',
        'short_name',
        'gender',
        'age_group',
        'hide_from_rankings',
        'allow_mentor_prospect',
        'star_rating',
        'score_sheet_type_id',
        'is_active',
        'min_teams',
        'max_teams',
        'region_id',
        'registration_fee',
        'information',
        'tournament_type_id',
        'tournament_structure_id',
        'tournament_registration_type_id',
        'start_date',
        'end_date',
        'ref_nomination_id',
        'officials_type_id',
        'levels',
        'set_game_strategy_id',
        'moving_strategy_id',
        'player_license_type_id',
        'penalty_type_id',
        'show_birth_in_score_sheet',
        'tournament_configs_id',
        'league_id',
    ];

    protected $casts = [
        'hide_from_rankings' => 'boolean',
        'allow_mentor_prospect' => 'boolean',
        'star_rating' => 'integer',
        'score_sheet_type_id' => 'integer',
        'is_active' => 'boolean',
        'min_teams' => 'integer',
        'max_teams' => 'integer',
        'region_id' => 'integer',
        'registration_fee' => 'integer',
        'tournament_type_id' => 'integer',
        'tournament_structure_id' => 'integer',
        'tournament_registration_type_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'ref_nomination_id' => 'integer',
        'officials_type_id' => 'integer',
        'levels' => 'integer',
        'set_game_strategy_id' => 'integer',
        'moving_strategy_id' => 'integer',
        'player_license_type_id' => 'integer',
        'penalty_type_id' => 'integer',
        'show_birth_in_score_sheet' => 'boolean',
        'tournament_configs_id' => 'integer',
        'league_id' => 'integer',
    ];

    public function league()
    {
        return $this->belongsTo(League::class, 'league_id');
    }

    public function tournamentConfig()
    {
        return $this->belongsTo(TournamentConfig::class, 'tournament_configs_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_tournament_groups', 'tournament_group_id', 'team_id');
    }

    public function teamTournamentGroups()
    {
        return $this->hasMany(TeamTournamentGroup::class, 'tournament_group_id');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'tournament_group_id');
    }

    public function games()
    {
        return $this->hasMany(Game::class, 'group_id');
    }

    public function tournaments()
    {
        return $this->hasMany(Tournament::class, 'tournament_group_id');
    }
}
