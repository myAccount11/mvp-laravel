<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachEducation extends Model
{
    use HasFactory;

    protected $table = 'coach_education';

    protected $fillable = [
        'module',
        'date',
        'comment',
        'hours',
        'coach_id',
        'mvp',
        'deleted',
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'integer',
        'coach_id' => 'integer',
        'mvp' => 'integer',
        'deleted' => 'boolean',
    ];

    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coach_id');
    }

    public function coachEducationLicenseTypes()
    {
        return $this->hasMany(CoachEducationLicenseType::class, 'coach_education_id');
    }
}

