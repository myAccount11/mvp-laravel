<?php

namespace App\Models\V5;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'email',
        'password',
        'name',
        'picture',
        'disable_emails',
        'license',
        'gender',
        'birth_year',
        'birth_month',
        'birth_day',
        'nationality',
        'address_line1',
        'address_line2',
        'postal_code',
        'city',
        'country',
        'phone_numbers',
        'debtor_number',
        'latlng',
        'google_account_id',
        'is_verified',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'disable_emails' => 'boolean',
        'is_verified' => 'boolean',
        'license' => 'integer',
        'birth_year' => 'integer',
        'birth_month' => 'integer',
        'birth_day' => 'integer',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
            ->withPivot('club_id', 'team_id', 'season_sport_id', 'user_role_approved_by_user_id', 'user_role_spec');
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }

    public function seasonSports()
    {
        return $this->belongsToMany(SeasonSport::class, 'user_season_sports', 'user_id', 'season_sport_id')
            ->wherePivot('is_active', true);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'user_roles', 'user_id', 'team_id');
    }

    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'user_roles', 'user_id', 'club_id');
    }

    public function userSeasonSports()
    {
        return $this->hasMany(UserSeasonSport::class, 'user_id');
    }
}

