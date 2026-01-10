<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    protected $table = 'courts';

    protected $fillable = [
        'name',
        'is_active',
        'venue_id',
        'length',
        'width',
        'side_space',
        'end_space',
        'deleted',
        'parent_id',
        'court_number',
        'court_type',
        'court_surface',
        'court_size',
        'court_lighting',
        'court_heating',
        'court_ventilation',
        'court_sound_system',
        'court_score_board',
        'court_seating_capacity',
        'court_handicap_accessible',
        'court_notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'venue_id' => 'integer',
        'length' => 'integer',
        'width' => 'integer',
        'side_space' => 'integer',
        'end_space' => 'integer',
        'deleted' => 'boolean',
        'parent_id' => 'integer',
        'court_number' => 'integer',
    ];

    public function childCourts()
    {
        return $this->hasMany(Court::class, 'parent_id');
    }

    public function parentCourt()
    {
        return $this->belongsTo(Court::class, 'parent_id');
    }

    public function courtUsages()
    {
        return $this->hasMany(CourtUsage::class, 'court_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function requirements()
    {
        return $this->belongsToMany(CourtRequirement::class, 'court_usage', 'court_id', 'court_requirement_id');
    }

    public function courtPriorities()
    {
        return $this->hasMany(CourtPriority::class, 'court_id');
    }
}

