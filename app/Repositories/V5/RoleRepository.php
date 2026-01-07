<?php

namespace App\Repositories\V5;

use App\Models\V5\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }

    public function findByValue($value)
    {
        return $this->model->where('value', $value)->first();
    }
}

