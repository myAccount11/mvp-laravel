<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtUsage extends Model
{
    use HasFactory;

    protected $table = 'court_usage';

    public $timestamps = false;

    protected $fillable = [
        'court_usage_count',
        'court_requirement_id',
        'court_id',
    ];

    protected $casts = [
        'court_usage_count' => 'integer',
        'court_requirement_id' => 'integer',
        'court_id' => 'integer',
    ];

    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id');
    }

    public function courtRequirement()
    {
        return $this->belongsTo(CourtRequirement::class, 'court_requirement_id');
    }
}

