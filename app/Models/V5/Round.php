<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    use HasFactory;

    protected $table = 'rounds';

    public $timestamps = false;

    protected $fillable = [
        'number',
        'tournament_id',
        'from_date',
        'to_date',
        'week',
        'year',
        'type',
        'force_cross',
        'deleted',
    ];

    protected $casts = [
        'number' => 'integer',
        'tournament_id' => 'integer',
        'from_date' => 'date',
        'to_date' => 'date',
        'week' => 'integer',
        'year' => 'integer',
        'type' => 'integer',
        'force_cross' => 'boolean',
        'deleted' => 'boolean',
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class, 'tournament_id');
    }
}

