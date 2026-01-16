<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $table = 'tournaments';

    protected $fillable = [
        'name',
        'short_name',
        'gender',
        'age_group',
        'is_active',
        'region_id',
        'start_date',
        'end_date',
        'standing_group_count',
        'cross_pool_game_count',
        'cross_standing_group_game_count',
        'round_type',
        'information',
        'team_count',
        'deleted',
        'tournament_structure_id',
        'tournament_registration_type_id',
        'league_id',
        'free_reschedule_until_date',
        'registration_dead_line',
        'minimum_warmup_minutes',
        'expected_duration_minutes',
        'earliest_start',
        'latest_start',
        'season_sport_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'standing_group_count' => 'integer',
        'cross_pool_game_count' => 'integer',
        'cross_standing_group_game_count' => 'integer',
        'round_type' => 'integer',
        'team_count' => 'integer',
        'deleted' => 'boolean',
        'region_id' => 'integer',
        'tournament_structure_id' => 'integer',
        'tournament_registration_type_id' => 'integer',
        'league_id' => 'integer',
        'free_reschedule_until_date' => 'date',
        'registration_dead_line' => 'date',
        'minimum_warmup_minutes' => 'integer',
        'expected_duration_minutes' => 'integer',
        'season_sport_id' => 'integer',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function league()
    {
        return $this->belongsTo(League::class, 'league_id');
    }

    public function tournamentStructure()
    {
        return $this->belongsTo(TournamentStructure::class, 'tournament_structure_id');
    }

    public function tournamentRegistrationType()
    {
        return $this->belongsTo(TournamentRegistrationType::class, 'tournament_registration_type_id');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'tournament_id');
    }

    public function games()
    {
        return $this->hasMany(Game::class, 'tournament_id');
    }

    /**
     * Set the region_id attribute, converting 0 to null
     */
    public function setRegionIdAttribute($value)
    {
        // Convert 0, '0', or empty string to null
        if ($value == 0 || $value === '0' || $value === '') {
            $this->attributes['region_id'] = null;
        } else {
            $this->attributes['region_id'] = $value;
        }
    }

    public function rounds()
    {
        return $this->hasMany(Round::class, 'tournament_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_tournaments', 'tournament_id', 'team_id')
            ->withPivot('start_points');
    }

    public function teamTournaments()
    {
        return $this->hasMany(TeamTournament::class, 'tournament_id');
    }

    public function tournamentConfig()
    {
        return $this->hasOne(TournamentConfig::class, 'tournament_id');
    }

    public function tournamentMatches()
    {
        return $this->hasMany(Game::class, 'tournament_id');
    }

    public function tournamentGroups()
    {
        return $this->hasMany(TournamentGroup::class, 'tournament_id');
    }

}
