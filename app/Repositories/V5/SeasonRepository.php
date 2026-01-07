<?php

namespace App\Repositories\V5;

use App\Models\V5\Season;
use App\Repositories\BaseRepository;

class SeasonRepository extends BaseRepository
{
    public function __construct(Season $model)
    {
        parent::__construct($model);
    }
}

