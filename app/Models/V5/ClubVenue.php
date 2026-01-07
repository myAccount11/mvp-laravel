<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubVenue extends Model
{
    use HasFactory;

    protected $table = 'club_venues';

    protected $fillable = [
        'club_id',
        'venue_id',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
}

