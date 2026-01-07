<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    public $timestamps = false;

    protected $fillable = [
        'start_time',
        'end_time',
        'text',
        'type_id',
        'time_slot_id',
        'club_id',
        'team_id',
        'game_id',
        'user_id',
        'age_group',
        'is_deleted',
    ];

    protected $casts = [
        'type_id' => 'integer',
        'time_slot_id' => 'integer',
        'club_id' => 'integer',
        'team_id' => 'integer',
        'game_id' => 'integer',
        'user_id' => 'integer',
        'is_deleted' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(ReservationType::class, 'type_id');
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot_id');
    }
}

