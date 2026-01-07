<?php

namespace App\Repositories\V5;

use App\Models\V5\ReservationType;
use App\Repositories\BaseRepository;

class ReservationTypeRepository extends BaseRepository
{
    public function __construct(ReservationType $model)
    {
        parent::__construct($model);
    }
}

