<?php

namespace App\Repositories\V5;

use App\Models\V5\Region;
use App\Repositories\BaseRepository;

class RegionRepository extends BaseRepository
{
    public function __construct(Region $model)
    {
        parent::__construct($model);
    }
}

