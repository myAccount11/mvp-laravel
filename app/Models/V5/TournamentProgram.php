<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Model;

class TournamentProgram extends Model
{
    protected $table = 'tournament_programs';
    
    public $timestamps = false;

    protected $fillable = [
        'name',
        'season_sport_id',
    ];

    protected $casts = [
        'season_sport_id' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(TournamentProgramItem::class, 'tournament_program_id');
    }
}

