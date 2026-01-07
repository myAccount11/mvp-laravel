<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationType extends Model
{
    use HasFactory;

    protected $table = 'reservation_types';

    public $timestamps = false;

    protected $fillable = [
        'text',
    ];
}

