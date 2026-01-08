<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $table = 'teams';

    protected $fillable = [
        'club_id',
        'local_name',
        'deleted',
        'ancestor_id',
        'cal_key',
        'license',
        'tournament_name',
        'gender',
        'age_group',
        'official_type_id',
        'official_team_id',
        'club_rank',
    ];

    protected $casts = [
        'deleted' => 'boolean',
        'license' => 'integer',
        'ancestor_id' => 'integer',
        'official_type_id' => 'integer',
        'official_team_id' => 'integer',
        'club_rank' => 'integer',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'team_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'team_id', 'user_id')
            ->withPivot('role_id', 'club_id', 'season_sport_id', 'user_role_approved_by_user_id', 'user_role_spec');
    }

    public function teamSeasonSports()
    {
        return $this->hasMany(TeamSeasonSport::class, 'team_id');
    }

    public function tournamentGroups()
    {
        return $this->belongsToMany(TournamentGroup::class, 'team_tournament_groups', 'team_id', 'tournament_group_id');
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'team_tournaments', 'team_id', 'tournament_id')
            ->withPivot(['id']);
    }
}

