<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Model;

class TournamentStructure extends Model
{
    protected $table = 'tournament_structures';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'value'
    ];

    const REGULAR_LEAGUE_STRUCTURE = [
        'value' => 'regular_league',
        'name'  => 'Regular League',
    ];

    const PLAYOFFS_STRUCTURE = [
        'value' => 'playoffs',
        'name'  => 'Playoffs',
    ];

    const GROUP_STAGE_AND_PLAYOFF_STRUCTURE = [
        'value' => 'group_stage_and_playoffs',
        'name'  => 'Group Stage + Playoffs',
    ];

    public static function getStructures()
    {
        return [
            self::REGULAR_LEAGUE_STRUCTURE,
            self::PLAYOFFS_STRUCTURE,
            self::GROUP_STAGE_AND_PLAYOFF_STRUCTURE,
        ];
    }

    public function tournaments()
    {
        return $this->hasMany(Tournament::class, 'tournament_structure_id');
    }
}

