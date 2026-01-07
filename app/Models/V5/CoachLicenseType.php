<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachLicenseType extends Model
{
    use HasFactory;

    protected $table = 'coach_license_type';

    protected $fillable = [
        'name',
        'description',
    ];
}

