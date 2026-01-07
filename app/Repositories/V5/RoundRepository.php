<?php

namespace App\Repositories\V5;

use App\Models\V5\Round;
use App\Repositories\BaseRepository;

class RoundRepository extends BaseRepository
{
    public function __construct(Round $model)
    {
        parent::__construct($model);
    }
}

