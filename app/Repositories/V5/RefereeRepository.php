<?php

namespace App\Repositories\V5;

use App\Models\V5\Referee;
use App\Repositories\BaseRepository;

class RefereeRepository extends BaseRepository
{
    public function __construct(Referee $model)
    {
        parent::__construct($model);
    }
}

