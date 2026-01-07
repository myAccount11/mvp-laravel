<?php

namespace App\Repositories\V5;

use App\Models\V5\GamePlan;
use App\Repositories\BaseRepository;

class GamePlanRepository extends BaseRepository
{
    public function __construct(GamePlan $model)
    {
        parent::__construct($model);
    }
}

