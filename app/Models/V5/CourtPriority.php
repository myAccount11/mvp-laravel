<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtPriority extends Model
{
    use HasFactory;

    protected $table = 'court_priority';

    protected $fillable = [
        'team_id',
        'club_id',
        'court_id',
        'court_priority_number',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'club_id' => 'integer',
        'court_id' => 'integer',
        'court_priority_number' => 'integer',
    ];

    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}

