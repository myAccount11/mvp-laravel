<?php

namespace App\Repositories\V5;

use App\Models\V5\BlockedPeriod;
use App\Repositories\BaseRepository;

class BlockedPeriodRepository extends BaseRepository
{
    public function __construct(BlockedPeriod $model)
    {
        parent::__construct($model);
    }
}

