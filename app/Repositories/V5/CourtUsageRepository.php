<?php

namespace App\Repositories\V5;

use App\Models\V5\CourtUsage;
use App\Repositories\BaseRepository;

class CourtUsageRepository extends BaseRepository
{
    public function __construct(CourtUsage $model)
    {
        parent::__construct($model);
    }
}

