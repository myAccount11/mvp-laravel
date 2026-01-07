<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerLicense extends Model
{
    use HasFactory;

    protected $table = 'player_license';

    public $timestamps = false;

    protected $fillable = [
        'start',
        'end',
        'club_name',
        'club_id',
        'player_id',
        'status',
        'season_sport_id',
        'identity_id',
        'on_contract',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'club_id' => 'integer',
        'player_id' => 'integer',
        'season_sport_id' => 'integer',
        'identity_id' => 'integer',
        'on_contract' => 'integer',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}

