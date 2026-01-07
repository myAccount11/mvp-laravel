<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoachLicense extends Model
{
    use HasFactory;

    protected $table = 'coach_license';

    protected $fillable = [
        'coach_license_type_id',
        'start',
        'end',
        'deleted',
        'coach_id',
    ];

    protected $casts = [
        'coach_license_type_id' => 'integer',
        'start' => 'date',
        'end' => 'date',
        'deleted' => 'boolean',
        'coach_id' => 'integer',
    ];

    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coach_id');
    }

    public function coachLicenseType()
    {
        return $this->belongsTo(CoachLicenseType::class, 'coach_license_type_id');
    }
}

