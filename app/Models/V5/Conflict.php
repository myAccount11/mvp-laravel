<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conflict extends Model
{
    use HasFactory;

    protected $table = 'conflicts';

    const UPDATED_AT = null;

    protected $fillable = [
        'game_id',
        'start_time',
        'blocked_association',
        'blocked_team',
        'games_to_close',
        'games_on_court',
        'reservations',
        'coaches',
        'has_court',
        'ignore_associations',
        'ignore_home',
        'ignore_away',
    ];

    protected $casts = [
        'game_id' => 'integer',
        'ignore_associations' => 'boolean',
        'ignore_home' => 'boolean',
        'ignore_away' => 'boolean',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
}

