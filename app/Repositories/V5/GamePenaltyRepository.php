<?php

namespace App\Repositories\V5;

use App\Models\V5\GamePenalty;
use App\Repositories\BaseRepository;

class GamePenaltyRepository extends BaseRepository
{
    public function __construct(GamePenalty $model)
    {
        parent::__construct($model);
    }
}

