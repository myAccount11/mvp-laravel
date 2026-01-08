<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Model;

class TournamentStructure extends Model
{
    protected $table = 'tournament_structures';
    
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function tournamentGroups()
    {
        return $this->hasMany(TournamentGroup::class, 'tournament_structure_id');
    }
}

