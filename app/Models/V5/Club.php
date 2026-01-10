<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $table = 'clubs';

    protected $fillable = [
        'name',
        'building',
        'address_line1',
        'address_line2',
        'postal_code',
        'postal_city',
        'country',
        'region_id',
        'phone_number1',
        'phone_number2',
        'email',
        'public_notes',
        'internal_notes',
        'web_address',
        'short_name',
        'deleted',
        'district',
        'status',
        'is_active',
        'cal_key',
        'license',
    ];

    protected $casts = [
        'deleted' => 'boolean',
        'is_active' => 'boolean',
        'license' => 'integer',
    ];

    public function teams()
    {
        return $this->hasMany(Team::class, 'club_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'club_id', 'user_id')
            ->withPivot('role_id', 'team_id', 'season_sport_id', 'user_role_approved_by_user_id', 'user_role_spec');
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'club_id');
    }

    public function clubSeasonSports()
    {
        return $this->hasMany(ClubSeasonSport::class, 'club_id');
    }

    public function clubVenues()
    {
        return $this->hasMany(ClubVenue::class, 'club_id');
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'club_id', 'user_id')
            ->wherePivot('role_id', 1)
            ->wherePivot('user_role_approved_by_user_id', '>', 0)
            ->withPivot('role_id', 'team_id', 'season_sport_id', 'user_role_approved_by_user_id', 'user_role_spec');
    }
}

