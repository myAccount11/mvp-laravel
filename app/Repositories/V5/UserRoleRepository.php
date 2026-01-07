<?php

namespace App\Repositories\V5;

use App\Models\V5\UserRole;
use App\Repositories\BaseRepository;

class UserRoleRepository extends BaseRepository
{
    public function __construct(UserRole $model)
    {
        parent::__construct($model);
    }
}

