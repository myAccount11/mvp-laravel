<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedPeriodTournament extends Model
{
    use HasFactory;

    protected $table = 'blocked_periods_tournaments';

    public $timestamps = false;

    protected $fillable = [
        'blocked_period_id',
        'tournament_id',
    ];

    protected $casts = [
        'blocked_period_id' => 'integer',
        'tournament_id' => 'integer',
    ];

    public function blockedPeriod()
    {
        return $this->belongsTo(BlockedPeriod::class, 'blocked_period_id');
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }
}

