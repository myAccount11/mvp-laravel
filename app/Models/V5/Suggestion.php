<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    use HasFactory;

    protected $table = 'suggestions';

    public $timestamps = false;

    protected $fillable = [
        'game_id',
        'date',
        'time',
        'court_id',
        'requested_by',
        'accepted_by',
        'rejected_by',
        'approved_by',
        'requested_date',
        'accepted_date',
        'rejected_date',
        'approved_date',
    ];

    protected $casts = [
        'game_id' => 'integer',
        'date' => 'date',
        'court_id' => 'integer',
        'requested_by' => 'integer',
        'accepted_by' => 'integer',
        'rejected_by' => 'integer',
        'approved_by' => 'integer',
        'requested_date' => 'datetime',
        'accepted_date' => 'datetime',
        'rejected_date' => 'datetime',
        'approved_date' => 'datetime',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function court()
    {
        return $this->belongsTo(Court::class, 'court_id');
    }

    public function requestedByUser()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function acceptedByUser()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

