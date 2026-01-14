<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'value',
        'description',
    ];
    const SUPER_ADMIN = 'super_admin';
    const CLUB_MANAGER = 'club_manager';
    const HEAD_COACH = 'head_coach';
    const ASSISTANT_COACH = 'assistant_coach';
    const TEAM_MANAGER = 'team_manager';
    const ASSOCIATION_ADMIN = 'association_admin';
    const PLAYER = 'player';

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
            ->withPivot('club_id', 'team_id', 'season_sport_id', 'user_role_approved_by_user_id', 'user_role_spec');
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'role_id');
    }
}

