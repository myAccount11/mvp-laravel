<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $table = 'sports';

    protected $fillable = [
        'name',
    ];

    public function seasonSports()
    {
        return $this->hasMany(SeasonSport::class, 'sport_id');
    }
}

