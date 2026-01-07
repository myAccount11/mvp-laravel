<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtRequirement extends Model
{
    use HasFactory;

    protected $table = 'court_requirements';

    protected $fillable = [
        'name',
        'description',
    ];
}

