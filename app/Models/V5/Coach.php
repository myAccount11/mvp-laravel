<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    use HasFactory;

    protected $table = 'coach';

    protected $fillable = [
        'license',
        'person_id',
        'level',
        'start',
        'end',
        'master_license',
    ];

    protected $casts = [
        'license' => 'integer',
        'person_id' => 'integer',
        'start' => 'date',
        'end' => 'date',
        'master_license' => 'integer',
    ];

    public function coachHistories()
    {
        return $this->hasMany(CoachHistory::class, 'coach_id');
    }

    public function coachEducation()
    {
        return $this->hasMany(CoachEducation::class, 'coach_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function coachLicenses()
    {
        return $this->hasMany(CoachLicense::class, 'coach_id');
    }
}

