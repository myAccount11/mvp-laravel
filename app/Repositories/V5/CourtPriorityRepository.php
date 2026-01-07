<?php

namespace App\Repositories\V5;

use App\Models\V5\CourtPriority;
use App\Repositories\BaseRepository;

class CourtPriorityRepository extends BaseRepository
{
    public function __construct(CourtPriority $model)
    {
        parent::__construct($model);
    }
}

