<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Model;

class TournamentRegistrationType extends Model
{
    protected $table = 'tournament_registration_types';
    
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function tournaments()
    {
        return $this->hasMany(Tournament::class, 'tournament_registration_type_id');
    }
}

