<?php

namespace App\Repositories\V5;

use App\Models\V5\Coach;
use App\Repositories\BaseRepository;

class CoachRepository extends BaseRepository
{
    public function __construct(Coach $model)
    {
        parent::__construct($model);
    }
}

