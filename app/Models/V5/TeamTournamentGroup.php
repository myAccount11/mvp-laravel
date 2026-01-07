<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamTournamentGroup extends Model
{
    use HasFactory;

    protected $table = 'team_tournament_group';

    public $timestamps = false;

    protected $fillable = [
        'team_id',
        'tournament_group_id',
        'level',
        'is_deleted',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'tournament_group_id' => 'integer',
        'level' => 'integer',
        'is_deleted' => 'boolean',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function tournamentGroup()
    {
        return $this->belongsTo(TournamentGroup::class, 'tournament_group_id');
    }
}

