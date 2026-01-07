<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameNote extends Model
{
    use HasFactory;

    protected $table = 'game_notes';

    public $timestamps = false;

    protected $fillable = [
        'game_id',
        'user_id',
        'text',
    ];

    protected $casts = [
        'game_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

