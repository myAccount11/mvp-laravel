<?php

namespace App\Repositories\V5;

use App\Models\V5\Pool;
use App\Repositories\BaseRepository;

class PoolRepository extends BaseRepository
{
    public function __construct(Pool $model)
    {
        parent::__construct($model);
    }
}

