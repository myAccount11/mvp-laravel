<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamePenalty extends Model
{
    use HasFactory;

    protected $table = 'game_penalties';

    protected $fillable = [
        'game_id',
        'value',
        'number',
        'side',
    ];

    protected $casts = [
        'game_id' => 'integer',
        'value' => 'integer',
        'number' => 'integer',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
}

