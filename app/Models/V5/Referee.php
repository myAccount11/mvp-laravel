<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referee extends Model
{
    use HasFactory;

    protected $table = 'referee';

    protected $fillable = [
        'license',
        'is_active',
        'recalc_coordinates',
        'user_id',
        'prio',
        'prio_max',
        'only_with_better',
        'max_star_rating',
        'mentor',
        'prospect',
        'reserve',
        'showwin_mvp_min',
        'showwin_mvp_max',
        'showwin_mvp_max_distance',
        'showwin_mvp_notify_on_new',
        'commisioner_level',
        'evaluator_level',
        'can_three',
    ];

    protected $casts = [
        'license' => 'integer',
        'is_active' => 'boolean',
        'recalc_coordinates' => 'integer',
        'user_id' => 'integer',
        'prio' => 'integer',
        'prio_max' => 'integer',
        'only_with_better' => 'boolean',
        'max_star_rating' => 'integer',
        'mentor' => 'boolean',
        'prospect' => 'boolean',
        'reserve' => 'boolean',
        'showwin_mvp_min' => 'integer',
        'showwin_mvp_max' => 'integer',
        'showwin_mvp_max_distance' => 'integer',
        'showwin_mvp_notify_on_new' => 'integer',
        'commisioner_level' => 'integer',
        'evaluator_level' => 'integer',
        'can_three' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

