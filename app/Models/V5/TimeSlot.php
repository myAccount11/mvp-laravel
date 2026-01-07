<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $table = 'time_slots';

    public $timestamps = false;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'expiration',
        'court_id',
        'club_id',
        'season_sport_id',
        'is_deleted',
    ];

    protected $casts = [
        'date' => 'date',
        'expiration' => 'date',
        'court_id' => 'integer',
        'club_id' => 'integer',
        'season_sport_id' => 'integer',
        'is_deleted' => 'boolean',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function seasonSport()
    {
        return $this->belongsTo(SeasonSport::class, 'season_sport_id');
    }

    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'time_slot_id');
    }
}

