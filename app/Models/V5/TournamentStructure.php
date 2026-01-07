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
}

