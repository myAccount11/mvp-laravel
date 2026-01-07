<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedPeriodTournamentGroup extends Model
{
    use HasFactory;

    protected $table = 'blocked_periods_tournament_groups';

    public $timestamps = false;

    protected $fillable = [
        'blocked_period_id',
        'tournament_group_id',
    ];

    protected $casts = [
        'blocked_period_id' => 'integer',
        'tournament_group_id' => 'integer',
    ];

    public function blockedPeriod()
    {
        return $this->belongsTo(BlockedPeriod::class, 'blocked_period_id');
    }

    public function tournamentGroup()
    {
        return $this->belongsTo(TournamentGroup::class, 'tournament_group_id');
    }
}

