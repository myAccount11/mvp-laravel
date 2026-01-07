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

