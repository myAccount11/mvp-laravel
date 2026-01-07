<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_roles';

    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'user_id',
        'club_id',
        'team_id',
        'season_sport_id',
        'user_role_approved_by_user_id',
        'user_role_spec',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }
}

