<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $table = 'regions';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'season_sport_id',
    ];

    protected $casts = [
        'season_sport_id' => 'integer',
    ];
}

