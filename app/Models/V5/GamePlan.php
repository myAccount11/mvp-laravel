<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamePlan extends Model
{
    use HasFactory;

    protected $table = 'game_plan';

    protected $fillable = [
        'game_role_id',
        'status_id',
        'display',
        'user_id',
        'team_id',
        'game_id',
        'responsible_type',
        'responsible_id',
        'responsible_accepted_by_id',
        'responsible_accepted_time_stamp',
        'assigned_to_id',
        'assigned_accepted_by_id',
        'assigned_accepted_time_stamp',
        'fee',
        'ferry_fee',
        'food_fee',
        'game_fee',
        'driving_fee',
        'fee_changed_by',
        'original_fee',
        'fee_approved',
        'fee_final',
        'fee_comment',
        'fee_approved_by_ref_time_stamp',
        'fee_approved_by_final',
        'fee_ref',
    ];

    protected $casts = [
        'game_role_id' => 'integer',
        'status_id' => 'integer',
        'user_id' => 'integer',
        'team_id' => 'integer',
        'game_id' => 'integer',
        'responsible_id' => 'integer',
        'responsible_accepted_by_id' => 'integer',
        'responsible_accepted_time_stamp' => 'date',
        'assigned_to_id' => 'integer',
        'assigned_accepted_by_id' => 'integer',
        'assigned_accepted_time_stamp' => 'date',
        'fee' => 'integer',
        'ferry_fee' => 'integer',
        'food_fee' => 'integer',
        'game_fee' => 'integer',
        'driving_fee' => 'integer',
        'fee_changed_by' => 'integer',
        'original_fee' => 'integer',
        'fee_approved' => 'integer',
        'fee_final' => 'integer',
        'fee_approved_by_final' => 'integer',
        'fee_ref' => 'integer',
        'fee_approved_by_ref_time_stamp' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
}

