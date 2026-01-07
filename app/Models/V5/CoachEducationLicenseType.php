<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachEducationLicenseType extends Model
{
    use HasFactory;

    protected $table = 'coach_education_license_type';

    protected $fillable = [
        'coach_education_id',
        'coach_license_type_id',
    ];

    protected $casts = [
        'coach_education_id' => 'integer',
        'coach_license_type_id' => 'integer',
    ];
}

