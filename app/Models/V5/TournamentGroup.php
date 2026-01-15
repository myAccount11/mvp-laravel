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
        'is_active',
        'min_teams',
        'max_teams',
        'region_id',
        'information',
        'tournament_structure_id',
        'tournament_registration_type_id',
        'start_date',
        'end_date',
        'set_game_strategy_id',
        'moving_strategy_id',
        'league_id',
        'free_reschedule_until_date',
        'registration_dead_line',
        'minimum_warmup_minutes',
        'expected_duration_minutes',
        'earliest_start',
        'latest_start',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_teams' => 'integer',
        'max_teams' => 'integer',
        'region_id' => 'integer',
        'tournament_structure_id' => 'integer',
        'tournament_registration_type_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'set_game_strategy_id' => 'integer',
        'moving_strategy_id' => 'integer',
        'league_id' => 'integer',
        'free_reschedule_until_date' => 'date',
        'registration_dead_line' => 'date',
        'minimum_warmup_minutes' => 'integer',
        'expected_duration_minutes' => 'integer',
    ];

    public function league()
    {
        return $this->belongsTo(League::class, 'league_id');
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

    public function tournamentStructure()
    {
        return $this->belongsTo(TournamentStructure::class, 'tournament_structure_id');
    }

    public function tournamentRegistrationType()
    {
        return $this->belongsTo(TournamentRegistrationType::class, 'tournament_registration_type_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
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
}
