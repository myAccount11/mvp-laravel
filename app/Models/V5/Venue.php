<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $table = 'venues';

    protected $fillable = [
        'name',
        'address_line1',
        'address_line2',
        'postal_code',
        'postal_city',
        'country',
        'phone_number',
        'web_address',
        'is_active',
        'lat_lng',
        'place_id',
        'cal_key',
        'email',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function courts()
    {
        return $this->hasMany(Court::class, 'venue_id');
    }

    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_venues', 'venue_id', 'club_id');
    }

    public function clubVenues()
    {
        return $this->hasMany(ClubVenue::class, 'venue_id');
    }

    public function venueSeasonSports()
    {
        return $this->hasMany(VenueSeasonSport::class, 'venue_id');
    }
}

