<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Model;

class TournamentProgramItem extends Model
{
    protected $table = 'tournament_program_items';
    
    public $timestamps = false;

    protected $fillable = [
        'round_number',
        'home_key',
        'away_key',
        'tournament_program_id',
    ];

    protected $casts = [
        'round_number' => 'integer',
        'home_key' => 'integer',
        'away_key' => 'integer',
        'tournament_program_id' => 'integer',
    ];

    public function tournamentProgram()
    {
        return $this->belongsTo(TournamentProgram::class, 'tournament_program_id');
    }
}

