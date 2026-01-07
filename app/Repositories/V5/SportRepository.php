<?php

namespace App\Repositories\V5;

use App\Models\V5\Sport;
use App\Repositories\BaseRepository;

class SportRepository extends BaseRepository
{
    public function __construct(Sport $model)
    {
        parent::__construct($model);
    }
}

