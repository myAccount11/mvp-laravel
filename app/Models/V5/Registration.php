<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $table = 'registrations';

    public $timestamps = false;

    protected $fillable = [
        'count',
        'level',
        'tournament_group_id',
        'club_id',
    ];

    protected $casts = [
        'count' => 'integer',
        'level' => 'integer',
        'tournament_group_id' => 'integer',
        'club_id' => 'integer',
    ];

    public function tournamentGroup()
    {
        return $this->belongsTo(TournamentGroup::class, 'tournament_group_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}

