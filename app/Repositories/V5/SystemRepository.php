<?php

namespace App\Repositories\V5;

use App\Models\V5\System;
use App\Repositories\BaseRepository;

class SystemRepository extends BaseRepository
{
    public function __construct(System $model)
    {
        parent::__construct($model);
    }
}

