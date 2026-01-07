<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedPeriod extends Model
{
    use HasFactory;

    protected $table = 'blocked_periods';

    public $timestamps = false;

    protected $fillable = [
        'start_date',
        'end_date',
        'title',
        'description',
        'block_all',
        'season_sport_id',
        'is_deleted',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'block_all' => 'boolean',
        'season_sport_id' => 'integer',
        'is_deleted' => 'boolean',
    ];

    public function seasonSport()
    {
        return $this->belongsTo(SeasonSport::class, 'season_sport_id');
    }

    public function tournamentGroups()
    {
        return $this->belongsToMany(TournamentGroup::class, 'blocked_periods_tournament_groups', 'blocked_period_id', 'tournament_group_id');
    }

    public function blockedTournamentGroups()
    {
        return $this->hasMany(BlockedPeriodTournamentGroup::class, 'blocked_period_id');
    }
}

