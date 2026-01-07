<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentConfig extends Model
{
    use HasFactory;

    protected $table = 'tournament_configs';

    protected $fillable = [
        'number',
        'name',
        'game_dead_line',
        'free_reschedule_until_date',
        'registration_dead_line',
        'minimum_warmup_minutes',
        'expected_duration_minutes',
        'cooldown_minutes',
        'refs_per_game',
        'refs_from_associations',
        '24sec_required',
        'stats_required',
        'report_required',
        'default_score_sheet_type',
        'allow_adjusted_score_sheet',
        'in_active',
        'court_requirement_id',
        'season_sport_id',
        'deleted',
        'tl_edit_enabled',
        'games_hidden',
        'information',
        'earliest_start',
        'cm_time_set_until',
        'latest_start',
        'coach_license_type_id',
        'ref_prio',
        'allow_mentor_prospect',
        'star_rating',
        'transportation_fee',
    ];

    protected $casts = [
        'number' => 'integer',
        'game_dead_line' => 'date',
        'free_reschedule_until_date' => 'date',
        'registration_dead_line' => 'date',
        'minimum_warmup_minutes' => 'integer',
        'expected_duration_minutes' => 'integer',
        'cooldown_minutes' => 'integer',
        'refs_per_game' => 'integer',
        'refs_from_associations' => 'boolean',
        '24sec_required' => 'boolean',
        'stats_required' => 'boolean',
        'report_required' => 'boolean',
        'default_score_sheet_type' => 'integer',
        'allow_adjusted_score_sheet' => 'boolean',
        'in_active' => 'boolean',
        'court_requirement_id' => 'integer',
        'season_sport_id' => 'integer',
        'deleted' => 'boolean',
        'tl_edit_enabled' => 'boolean',
        'games_hidden' => 'boolean',
        'cm_time_set_until' => 'datetime',
        'coach_license_type_id' => 'integer',
        'ref_prio' => 'integer',
        'allow_mentor_prospect' => 'integer',
        'star_rating' => 'integer',
        'transportation_fee' => 'integer',
    ];
}

