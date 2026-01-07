<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $table = 'player';

    protected $fillable = [
        'license',
        'person_id',
        'jersey_number',
    ];

    protected $casts = [
        'person_id' => 'integer',
    ];

    public function playerLicenses()
    {
        return $this->hasMany(PlayerLicense::class, 'player_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
}

