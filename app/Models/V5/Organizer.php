<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizer extends Model
{
    use HasFactory;

    protected $table = 'organizers';

    protected $fillable = [
        'name',
    ];

    public function seasons()
    {
        return $this->hasMany(Season::class, 'organizer_id');
    }
}

