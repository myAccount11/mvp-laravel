<?php

namespace App\Repositories\V5;

use App\Models\V5\Court;
use App\Repositories\BaseRepository;

class CourtRepository extends BaseRepository
{
    public function __construct(Court $model)
    {
        parent::__construct($model);
    }
}

