<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameDraft extends Model
{
    use HasFactory;

    protected $table = 'game_drafts';

    public $timestamps = false;

    protected $fillable = [
        'round_id',
        'tournament_id',
        'term_date',
        'home_key',
        'away_key',
        'round_number',
        'pool_id_cross_master',
        'pool_id_cross_slave',
        'switch_optimized',
    ];

    protected $casts = [
        'round_id' => 'integer',
        'tournament_id' => 'integer',
        'term_date' => 'date',
        'home_key' => 'integer',
        'away_key' => 'integer',
        'round_number' => 'integer',
        'pool_id_cross_master' => 'integer',
        'pool_id_cross_slave' => 'integer',
        'switch_optimized' => 'boolean',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }

    public function round()
    {
        return $this->belongsTo(Round::class, 'round_id');
    }

    public function homeTeamTournament()
    {
        return $this->belongsTo(TeamTournament::class, 'home_key', 'pool_key');
    }

    public function guestTeamTournament()
    {
        return $this->belongsTo(TeamTournament::class, 'away_key', 'pool_key');
    }
}

