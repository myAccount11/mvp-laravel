<?php

namespace App\Repositories\V5;

use App\Models\V5\CoachHistory;
use App\Repositories\BaseRepository;

class CoachHistoryRepository extends BaseRepository
{
    public function __construct(CoachHistory $model)
    {
        parent::__construct($model);
    }
}

