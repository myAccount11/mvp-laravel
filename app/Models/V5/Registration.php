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
        'tournament_id',
        'club_id',
    ];

    protected $casts = [
        'count' => 'integer',
        'level' => 'integer',
        'tournament_id' => 'integer',
        'club_id' => 'integer',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}

