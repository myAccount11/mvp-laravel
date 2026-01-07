<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'person';

    protected $fillable = [
        'external_id',
        'email',
        'name',
        'season_sport_id',
        'deleted',
        'user_id',
        'address_line1',
        'address_line2',
        'postal_code',
        'city',
        'country',
        'phone_numbers',
        'is_active',
        'latlng',
        'place_id',
    ];

    protected $casts = [
        'external_id' => 'integer',
        'season_sport_id' => 'integer',
        'deleted' => 'boolean',
        'user_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function coaches()
    {
        return $this->hasMany(Coach::class, 'person_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function player()
    {
        return $this->hasOne(Player::class, 'person_id');
    }
}

