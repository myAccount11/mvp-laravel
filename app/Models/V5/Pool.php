<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    use HasFactory;

    protected $table = 'pools';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'tournament_id',
        'games_between',
        'teams_count',
        'deleted',
    ];

    protected $casts = [
        'tournament_id' => 'integer',
        'games_between' => 'integer',
        'teams_count' => 'integer',
        'deleted' => 'boolean',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }
}

